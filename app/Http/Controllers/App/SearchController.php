<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\SearchableEnum;
use App\Http\Controllers\Controller;
use App\Services\AccountSearch;
use App\ValueObjects\SearchResult;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request, ?string $type = null): View
    {
        $account = $request->user()->account;

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
        ]);

        $searchable = $type === null ? null : SearchableEnum::fromSlug($type);

        $search = new AccountSearch(
            account: $account,
            user: $request->user(),
            query: (string) ($validated['q'] ?? ''),
            type: $searchable,
        );

        return view('app.search.index', [
            'query' => $search->query(),
            'hasQuery' => $search->hasQuery(),
            'isQueryTooShort' => $search->isQueryTooShort(),
            'total' => $search->total(),
            'matched' => $search->matched(),
            'isCapped' => $search->isCapped(),
            'selectedType' => $searchable,
            'filters' => $this->filters($search),
            'groups' => $this->groups($search),
            'indexed' => $this->indexed($search),
        ]);
    }

    /**
     * The chips above the results, one per kind of record that matched, in the
     * order the groups appear below them.
     *
     * @return list<array{slug: ?string, label: string, count: int}>
     */
    private function filters(AccountSearch $search): array
    {
        $counts = $search->countsByType();

        if ($counts === []) {
            return [];
        }

        $filters = [['slug' => null, 'label' => __('All types'), 'count' => $search->total()]];

        foreach (SearchableEnum::cases() as $type) {
            if (! isset($counts[$type->value])) {
                continue;
            }

            $filters[] = [
                'slug' => $type->slug(),
                'label' => $type->pluralLabel(),
                'count' => $counts[$type->value],
            ];
        }

        return $filters;
    }

    /**
     * The result rows, flattened out of their models so the view never touches
     * an encrypted attribute.
     *
     * @return list<array{label: string, badge: string, badgeClasses: string, count: int, rows: list<array<string, mixed>>}>
     */
    private function groups(AccountSearch $search): array
    {
        return $search->groups()
            ->map(fn (array $group): array => [
                'label' => $group['type']->pluralLabel(),
                'badge' => $group['type']->label(),
                'badgeClasses' => $group['type']->badgeClasses(),
                'icon' => $group['type']->icon(),
                'count' => $group['results']->count(),
                'rows' => $group['results']->map(fn (SearchResult $result): array => [
                    'id' => $result->id,
                    'title' => $result->title,
                    'context' => $result->context,
                    'collectionName' => $result->collectionName,
                    'url' => $result->url,
                    'thumbnailUrl' => $result->thumbnailUrl,
                    'matched' => $result->isTitleMatch() ? __('Name match') : __('Text match'),
                ])->all(),
            ])
            ->all();
    }

    /**
     * What the account has indexed, shown before anything is typed.
     *
     * @return list<array{label: string, count: int}>
     */
    private function indexed(AccountSearch $search): array
    {
        if ($search->hasQuery()) {
            return [];
        }

        $counts = $search->indexedCounts();

        return new Collection(SearchableEnum::cases())
            ->map(fn (SearchableEnum $type): array => [
                'label' => $type->pluralLabel(),
                'icon' => $type->icon(),
                'count' => $counts[$type->value] ?? 0,
            ])
            ->all();
    }
}
