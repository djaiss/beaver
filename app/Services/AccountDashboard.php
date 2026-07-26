<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The numbers behind the dashboard, read across the whole account.
 *
 * This is the account-wide sibling of CatalogStatistics and reads the same way:
 * a copy is what carries the money, the condition and the location, and what a
 * copy is worth is its most recent valuation rather than a column on it. A copy
 * nobody has valued counts towards the copies and contributes nothing to any
 * amount.
 *
 * Every amount is in cents and is summed across collections. A collection may
 * name a currency of its own, but an account-wide roll-up has to settle on one,
 * so the dashboard reads its totals in the currency of the account.
 *
 * Names are encrypted, so nothing is grouped or sorted by name in SQL: the
 * aggregation happens on the foreign key and the names are resolved afterwards.
 */
class AccountDashboard
{
    private const int RECENT_ADDITIONS = 5;

    private const int FEATURED_COLLECTIONS = 4;

    private const int TOP_LOCATIONS = 5;

    public function __construct(
        private readonly Account $account,
    ) {}

    /**
     * The headline numbers of the account.
     *
     * @return array{collections: int, items: int, copies: int, valuedCopies: int, value: int, average: int, itemsAddedThisMonth: int, valueAddedThisMonth: int}
     */
    public function totals(): array
    {
        // The value is an alias over a subquery, so it has to be summed from
        // outside the query that builds it rather than aggregated in place.
        $copies = DB::query()->fromSub($this->valued(), 'valued')->count();
        $valuedCopies = DB::query()->fromSub($this->valued(), 'valued')->where('valued.value', '>', 0)->count();
        $value = (int) DB::query()->fromSub($this->valued(), 'valued')->sum('valued.value');

        $items = $this->items()->count();
        $startOfMonth = Carbon::now()->startOfMonth();

        $valuesByCopy = $this->valued()->pluck('value', 'id');
        $valueAddedThisMonth = $this->acquisitionDates()
            ->filter(fn (Carbon $date): bool => $date->greaterThanOrEqualTo($startOfMonth))
            ->keys()
            ->sum(fn (int $copyId): int => (int) ($valuesByCopy[$copyId] ?? 0));

        return [
            'collections' => $this->account->catalogs()->count(),
            'items' => $items,
            'copies' => $copies,
            'valuedCopies' => $valuedCopies,
            'value' => $value,
            'average' => $items === 0 ? 0 : (int) round($value / $items),
            'itemsAddedThisMonth' => $this->items()->where('items.created_at', '>=', $startOfMonth)->count(),
            'valueAddedThisMonth' => (int) $valueAddedThisMonth,
        ];
    }

    /**
     * The collections the dashboard puts forward, most recently touched first,
     * each with what it holds and what it is worth.
     *
     * @return list<array{catalog: Catalog, items: int, copies: int, value: int}>
     */
    public function collections(): array
    {
        $rows = DB::query()
            ->fromSub($this->valued(), 'valued')
            ->selectRaw('valued.catalog_id as catalog_id, count(*) as copies, sum(valued.value) as value')
            ->groupBy('valued.catalog_id')
            ->get()
            ->keyBy('catalog_id')
            ->map(fn (object $row): array => ['copies' => (int) $row->copies, 'value' => (int) $row->value]);

        return $this->account->catalogs()
            ->withCount('items')
            ->orderByDesc('updated_at')
            ->limit(self::FEATURED_COLLECTIONS)
            ->get()
            ->map(fn (Catalog $catalog): array => [
                'catalog' => $catalog,
                'items' => (int) $catalog->items_count,
                ...$rows->get($catalog->id, ['copies' => 0, 'value' => 0]),
            ])
            ->all();
    }

    /**
     * The newest items in the account, with the condition and the location of
     * their first copy. An item with several copies has no one answer for either,
     * so the row shows the first and says how many copies there are.
     *
     * @return list<array{item: Item, condition: ?string, location: ?string, copies: int}>
     */
    public function recentAdditions(): array
    {
        return $this->items()
            ->with(['catalog', 'mainPhoto', 'copies.itemCondition', 'copies.currentLocation'])
            ->orderByDesc('items.created_at')
            ->orderByDesc('items.id')
            ->limit(self::RECENT_ADDITIONS)
            ->get()
            ->map(function (Item $item): array {
                $copy = $item->copies->first();

                return [
                    'item' => $item,
                    'condition' => $copy?->itemCondition?->name,
                    'location' => $copy?->currentLocation?->name,
                    'copies' => $item->copies->count(),
                ];
            })
            ->all();
    }

    /**
     * Where custody stands across the account, both directions at once.
     *
     * The loans section reads one direction at a time and eager loads everything
     * a row needs; the dashboard only counts, so it loads the bare rows and asks
     * the model the same questions the section does.
     *
     * @return array{outgoing: int, incoming: int, overdue: int, dueSoon: int, planned: int, returned: int, deposits: array<string, int>}
     */
    public function loanSnapshot(): array
    {
        $loans = Loan::query()->forAccount($this->account)->get();

        return [
            'outgoing' => $loans->where('direction', LoanDirection::Outgoing)->where('status', LoanStatus::Active)->count(),
            'incoming' => $loans->where('direction', LoanDirection::Incoming)->where('status', LoanStatus::Active)->count(),
            'overdue' => $loans->filter(fn (Loan $loan): bool => $loan->isEffectivelyOverdue())->count(),
            'dueSoon' => $loans->filter(fn (Loan $loan): bool => $loan->isDueSoon())->count(),
            'planned' => $loans->where('status', LoanStatus::Planned)->count(),
            'returned' => $loans->where('status', LoanStatus::Returned)->count(),
            'deposits' => $loans
                ->filter(fn (Loan $loan): bool => $loan->status->isOpen() && $loan->deposit_amount !== null)
                ->groupBy('deposit_currency_code')
                ->map(fn (Collection $group): int => (int) $group->sum('deposit_amount'))
                ->all(),
        ];
    }

    /**
     * Where the value of the account physically sits, biggest first. A null label
     * stands for the copies filed under no location at all; the wording for those
     * is left to the view, which is where the translation extractor looks.
     *
     * @return list<array{label: ?string, value: int}>
     */
    public function valueByLocation(): array
    {
        $rows = DB::query()
            ->fromSub($this->valued(), 'valued')
            ->selectRaw('valued.current_location_id as current_location_id, sum(valued.value) as total')
            ->groupBy('valued.current_location_id')
            ->get();

        $names = Location::query()->whereIn('id', $rows->pluck('current_location_id')->filter())->get()->keyBy('id');

        return $rows
            ->map(fn (object $row): array => [
                'label' => $names->get($row->current_location_id)?->name,
                'value' => (int) $row->total,
            ])
            ->filter(fn (array $row): bool => $row['value'] > 0)
            ->sortByDesc('value')
            ->take(self::TOP_LOCATIONS)
            ->values()
            ->all();
    }

    /**
     * Every item of the account, as a query the callers narrow further.
     *
     * @return EloquentBuilder<Item>
     */
    private function items(): EloquentBuilder
    {
        return Item::query()->whereIn('catalog_id', $this->account->catalogs()->select('catalogs.id'));
    }

    /**
     * Every copy of the account, with what it is currently worth.
     *
     * A copy carries no value of its own, so the figure is the amount of its most
     * recent valuation, or zero when it has never been valued. The id breaks ties
     * on the date, so a copy valued twice in one day reads the second of the two
     * rather than picking arbitrarily.
     *
     * The collection comes along because the collection cards group by it, and
     * doing that here saves every caller its own join back to the items.
     */
    private function valued(): QueryBuilder
    {
        $latest = DB::table('valuations')
            ->select('valuations.amount')
            ->whereColumn('valuations.copy_id', 'copies.id')
            ->orderByDesc('valuations.valued_at')
            ->orderByDesc('valuations.id')
            ->limit(1);

        return DB::table('copies')
            ->join('items', 'items.id', '=', 'copies.item_id')
            ->join('catalogs', 'catalogs.id', '=', 'items.catalog_id')
            ->where('catalogs.account_id', $this->account->id)
            ->whereNull('catalogs.deleted_at')
            ->whereNull('items.deleted_at')
            ->whereNull('copies.deleted_at')
            ->select([
                'copies.id',
                'copies.item_id',
                'copies.current_location_id',
                'items.catalog_id',
            ])
            ->selectSub(
                DB::query()->selectRaw('coalesce(('.$latest->toSql().'), 0)')->mergeBindings($latest),
                'value',
            );
    }

    /**
     * When each copy of the account was acquired, keyed by copy id.
     *
     * The acquisition date is not stored on the copy. It is the date of the
     * earliest transaction that brought the copy in, so a copy with only a fee
     * against it has no acquisition date at all.
     *
     * @return Collection<int, Carbon>
     */
    private function acquisitionDates(): Collection
    {
        $acquiring = array_map(
            fn (TransactionType $type): string => $type->value,
            array_filter(TransactionType::cases(), fn (TransactionType $type): bool => $type->acquires()),
        );

        return DB::table('transactions')
            ->whereIn('transactions.copy_id', $this->valued()->select('copies.id'))
            ->whereIn('transactions.type', $acquiring)
            ->groupBy('transactions.copy_id')
            ->selectRaw('transactions.copy_id as copy_id, min(transactions.occurred_at) as acquired_at')
            ->pluck('acquired_at', 'copy_id')
            ->map(fn (string $date): Carbon => Carbon::parse($date));
    }
}
