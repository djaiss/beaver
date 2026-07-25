<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Searchable;
use App\Enums\SearchableEnum;
use App\Models\Account;
use App\Models\User;
use App\ValueObjects\SearchResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Answers "where is this in my account?" against the search index.
 *
 * Names and descriptions are encrypted, so nothing here can be matched with
 * LIKE. Every word typed is hashed the same way the index was built and has to
 * be present, which makes a query an AND across its words, and which is also
 * why a word is matched from its start: BlindIndex stores the prefixes.
 *
 * The whole read starts from the tokens table, filtered on the account, so a
 * record from another account is never even hydrated. Soft deleted records are
 * absent because their tokens go with them, so the trash stays out of search.
 */
class AccountSearch
{
    /**
     * How many results the screen shows. Enough to find what was meant, few
     * enough to read.
     */
    private const int MAX_RESULTS = 50;

    /**
     * How many matches are hydrated before ranking. Anything past this is a
     * query too vague to be answered by ranking anyway.
     */
    private const int MAX_CANDIDATES = 250;

    /**
     * @var Collection<int, SearchResult>|null
     */
    private ?Collection $results = null;

    /**
     * @var array<string, int>
     */
    private array $counts = [];

    private bool $queryTooShort = false;

    public function __construct(
        private readonly Account $account,
        private readonly User $user,
        private readonly string $query,
        private readonly ?SearchableEnum $type = null,
    ) {}

    public function query(): string
    {
        return trim($this->query);
    }

    public function hasQuery(): bool
    {
        return $this->query() !== '';
    }

    /**
     * Whether the query was made only of single letters. Those are never indexed
     * on their own, so the honest answer is "nothing found" rather than
     * everything.
     */
    public function isQueryTooShort(): bool
    {
        $this->results();

        return $this->queryTooShort;
    }

    /**
     * How many records match, per kind. The kinds that match nothing are absent.
     *
     * @return array<string, int>
     */
    public function countsByType(): array
    {
        $this->results();

        return $this->counts;
    }

    public function total(): int
    {
        return array_sum($this->countsByType());
    }

    /**
     * How many match once the chosen kind is taken into account.
     */
    public function matched(): int
    {
        if (! $this->type instanceof SearchableEnum) {
            return $this->total();
        }

        return $this->countsByType()[$this->type->value] ?? 0;
    }

    public function isCapped(): bool
    {
        return $this->matched() > self::MAX_RESULTS;
    }

    /**
     * The results, grouped by kind, in the order the kinds are declared.
     *
     * @return Collection<int, array{type: SearchableEnum, results: Collection<int, SearchResult>}>
     */
    public function groups(): Collection
    {
        $results = $this->results();

        return new Collection(SearchableEnum::cases())
            ->map(fn (SearchableEnum $type): array => [
                'type' => $type,
                'results' => $results->filter(fn (SearchResult $result): bool => $result->type === $type)->values(),
            ])
            ->filter(fn (array $group): bool => $group['results']->isNotEmpty())
            ->values();
    }

    /**
     * How many records of each kind the account has indexed, for the screen a
     * user sees before typing anything.
     *
     * @return array<string, int>
     */
    public function indexedCounts(): array
    {
        return DB::table('search_tokens')
            ->where('account_id', $this->account->id)
            ->whereIn('searchable_type', $this->searchableTypes())
            ->select('searchable_type', DB::raw('count(distinct searchable_id) as total'))
            ->groupBy('searchable_type')
            ->pluck('total', 'searchable_type')
            ->map(fn (int|string $total): int => (int) $total)
            ->all();
    }

    /**
     * @return Collection<int, SearchResult>
     */
    private function results(): Collection
    {
        if ($this->results instanceof Collection) {
            return $this->results;
        }

        if (! $this->hasQuery()) {
            return $this->results = new Collection;
        }

        $hashes = BlindIndex::hashesForQuery($this->query());

        if ($hashes === []) {
            $this->queryTooShort = true;

            return $this->results = new Collection;
        }

        $this->counts = $this->countMatches($hashes);

        return $this->results = $this->rank($this->candidates($hashes));
    }

    /**
     * Every record whose tokens cover all the words typed. Counting the distinct
     * tokens matched and demanding one per word is what makes the search an AND.
     *
     * @param  list<string>  $hashes
     */
    private function matches(array $hashes): Builder
    {
        return DB::table('search_tokens')
            ->select('searchable_type', 'searchable_id', DB::raw('min(weight) as score'))
            ->where('account_id', $this->account->id)
            ->whereIn('searchable_type', $this->searchableTypes())
            ->whereIn('token', $hashes)
            ->groupBy('searchable_type', 'searchable_id')
            ->havingRaw('count(distinct token) = ?', [count($hashes)]);
    }

    /**
     * @param  list<string>  $hashes
     * @return array<string, int>
     */
    private function countMatches(array $hashes): array
    {
        return DB::query()
            ->fromSub($this->matches($hashes), 'matches')
            ->select('searchable_type', DB::raw('count(*) as total'))
            ->groupBy('searchable_type')
            ->pluck('total', 'searchable_type')
            ->map(fn (int|string $total): int => (int) $total)
            ->all();
    }

    /**
     * The ids worth hydrating, heaviest match first. The score is the weight of
     * the weakest field a word matched in, so a record where every word landed
     * in the name comes out at 100 and one that only matched a description at 30.
     *
     * @param  list<string>  $hashes
     * @return Collection<int, stdClass>
     */
    private function candidates(array $hashes): Collection
    {
        $query = $this->matches($hashes);

        if ($this->type instanceof SearchableEnum) {
            $query->where('searchable_type', $this->type->value);
        }

        return $query
            ->orderByDesc('score')
            ->orderByDesc('searchable_id')
            ->limit(self::MAX_CANDIDATES)
            ->get();
    }

    /**
     * Load the records behind the ids, one query per kind, then order them.
     *
     * Loading through Eloquent is deliberate: it applies the soft delete scope,
     * so anything the index has not caught up with yet simply falls out here
     * rather than being offered as a result that 404s.
     *
     * @param  Collection<int, stdClass>  $candidates
     * @return Collection<int, SearchResult>
     */
    private function rank(Collection $candidates): Collection
    {
        $order = array_column(SearchableEnum::cases(), 'value');
        $rows = new Collection;

        foreach ($candidates->groupBy('searchable_type') as $type => $matches) {
            $searchable = SearchableEnum::from((string) $type);
            $scores = $matches->pluck('score', 'searchable_id');

            foreach ($this->records($searchable, $scores->keys()->all()) as $record) {
                $rows->push([
                    'record' => $record,
                    'type' => $searchable,
                    'score' => (int) $scores[$record->getKey()],
                    'rank' => array_search($searchable->value, $order, true),
                    'updated' => $record->updated_at?->getTimestamp() ?? 0,
                ]);
            }
        }

        return $rows
            ->sortBy([
                ['score', 'desc'],
                ['rank', 'asc'],
                ['updated', 'desc'],
            ])
            ->take(self::MAX_RESULTS)
            ->map(fn (array $row): SearchResult => $this->result($row['type'], $row['record'], $row['score']))
            ->values();
    }

    /**
     * The records of one kind, with everything their rows read already loaded.
     *
     * A record whose account no longer resolves is dropped: its parent has been
     * trashed since it was indexed, so it has nothing left to say and no screen
     * left to open.
     *
     * @param  list<int>  $ids
     * @return iterable<int, Model&Searchable>
     */
    private function records(SearchableEnum $type, array $ids): iterable
    {
        return $type->modelClass()::query()
            ->whereKey($ids)
            ->with($type->relations())
            ->withCount($type->relationCounts())
            ->get()
            ->filter(fn (Searchable $record): bool => $record->searchableAccountId() !== null);
    }

    private function result(SearchableEnum $type, Model&Searchable $record, int $score): SearchResult
    {
        return new SearchResult(
            type: $type,
            id: (int) $record->getKey(),
            title: $record->searchableTitle(),
            context: $record->searchableContext(),
            collectionName: $record->searchableCollectionName(),
            url: $record->searchableUrl(),
            thumbnailUrl: $record->searchableThumbnailUrl(),
            score: $score,
        );
    }

    /**
     * The kinds of record this user may be shown. A tag is only ever seen on a
     * screen owners and editors have, so a viewer is not offered one.
     *
     * @return list<string>
     */
    private function searchableTypes(): array
    {
        $manages = $this->account->allowsManagementBy($this->user);

        return array_values(array_map(
            fn (SearchableEnum $type): string => $type->value,
            array_filter(
                SearchableEnum::cases(),
                fn (SearchableEnum $type): bool => $manages || ! $type->needsManagementAccess(),
            ),
        ));
    }
}
