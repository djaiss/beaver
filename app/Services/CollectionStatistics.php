<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use App\Models\Collection as CollectionModel;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\Set;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * The numbers behind the statistics screen of a collection.
 *
 * Everything is read from the copies rather than the items, because a copy is
 * what carries the money, the condition and the location. Three copies of the
 * same comic are three amounts, not one.
 *
 * What a copy is worth is no longer a column on it. Valuations are append-only,
 * so the current figure is the most recent one, and every money number here is
 * built on that single reading rather than on a stored total.
 *
 * The two charts that run over time key off when a copy was acquired, which is
 * optional. Copies without a date are left out of those two rather than being
 * parked in an arbitrary month, so the screen reports how many were skipped.
 *
 * Names are encrypted, so nothing can be grouped or sorted by name in SQL. The
 * aggregation happens on the foreign key, and the names are resolved afterwards.
 *
 * A row with a null label is one that stands for the absence of the thing being
 * grouped by, the copies filed under no location for instance. The wording for
 * those is left to the view, which is where the translation extractor looks.
 */
class CollectionStatistics
{
    private const int MONTHS = 12;

    private const int TOP_ITEMS = 5;

    private const int NAMED_CATEGORIES = 6;

    public function __construct(
        private readonly CollectionModel $collection,
    ) {}

    /**
     * @return array{items: int, copies: int, value: int, average: int, itemsAddedThisMonth: int, valueAddedThisMonth: int, undatedCopies: int}
     */
    public function totals(): array
    {
        $items = $this->collection->items()->count();

        // The value is an alias over a subquery, so it has to be summed from
        // outside the query that builds it rather than aggregated in place.
        $copies = DB::query()->fromSub($this->valued(), 'valued')->count();
        $value = (int) DB::query()->fromSub($this->valued(), 'valued')->sum('valued.value');
        $startOfMonth = Carbon::now()->startOfMonth();

        $acquired = $this->acquisitionDates();
        $valuesByCopy = $this->valued()->pluck('value', 'id');

        $acquiredThisMonth = $acquired
            ->filter(fn (Carbon $date): bool => $date->greaterThanOrEqualTo($startOfMonth))
            ->keys()
            ->sum(fn (int $copyId): int => (int) ($valuesByCopy[$copyId] ?? 0));

        return [
            'items' => $items,
            'copies' => $copies,
            'value' => $value,
            'average' => $items === 0 ? 0 : (int) round($value / $items),
            'itemsAddedThisMonth' => $this->collection->items()->where('created_at', '>=', $startOfMonth)->count(),
            'valueAddedThisMonth' => (int) $acquiredThisMonth,
            'undatedCopies' => max(0, $copies - $acquired->count()),
        ];
    }

    /**
     * How far along the sets of the collection are, across every set that says
     * how many items it should end up with. A set holding more items than it
     * targets counts as done rather than as more than done.
     *
     * @return ?array{percentage: int, owned: int, target: int, remaining: int, sets: int}
     */
    public function setsCompletion(): ?array
    {
        $sets = $this->collection->sets()
            ->where('target_count', '>', 0)
            ->withCount('items')
            ->get();

        if ($sets->isEmpty()) {
            return null;
        }

        $target = (int) $sets->sum('target_count');
        $owned = (int) $sets->sum(fn (Set $set): int => min($set->items_count, (int) $set->target_count));

        return [
            'percentage' => (int) round(($owned / $target) * 100),
            'owned' => $owned,
            'target' => $target,
            'remaining' => $target - $owned,
            'sets' => $sets->count(),
        ];
    }

    /**
     * The estimated value the collection has accumulated, month by month. The
     * first point already carries everything acquired before the window, so the
     * line reads as a running total and not as a year of purchases.
     *
     * @return list<array{label: string, value: int}>
     */
    public function valueOverTime(): array
    {
        $start = $this->windowStart();
        $values = $this->valued()->pluck('value', 'id');
        $acquired = $this->acquisitionDates();

        $monthly = $acquired
            ->filter(fn (Carbon $date): bool => $date->greaterThanOrEqualTo($start))
            ->groupBy(fn (Carbon $date): string => $date->format('Y-m'))
            ->map(fn (Collection $dates): int => $dates->keys()->sum(fn (int $copyId): int => (int) ($values[$copyId] ?? 0)));

        $running = (int) $acquired
            ->filter(fn (Carbon $date): bool => $date->lessThan($start))
            ->keys()
            ->sum(fn (int $copyId): int => (int) ($values[$copyId] ?? 0));

        return array_map(function (Carbon $month) use ($monthly, &$running): array {
            $running += $monthly->get($month->format('Y-m'), 0);

            return ['label' => $month->translatedFormat('M'), 'value' => $running];
        }, $this->months());
    }

    /**
     * How many copies were acquired each month of the window.
     *
     * @return list<array{label: string, count: int}>
     */
    public function acquisitionsPerMonth(): array
    {
        $monthly = $this->acquisitionDates()
            ->filter(fn (Carbon $date): bool => $date->greaterThanOrEqualTo($this->windowStart()))
            ->groupBy(fn (Carbon $date): string => $date->format('Y-m'))
            ->map(fn (Collection $dates): int => $dates->count());

        return array_map(fn (Carbon $month): array => [
            'label' => $month->translatedFormat('M'),
            'count' => $monthly->get($month->format('Y-m'), 0),
        ], $this->months());
    }

    /**
     * How the items spread across the categories of the collection. Items are
     * counted against the category they sit in, so a parent category does not
     * absorb what is filed under its children.
     *
     * A collection can hold far more categories than a legend can show, so only
     * the biggest ones are named and the tail is summed into a single slice.
     *
     * @return list<array{label: ?string, other: bool, count: int, percentage: int}>
     */
    public function byCategory(): array
    {
        $categories = $this->collection->categories()
            ->withCount('items')
            ->get()
            ->filter(fn (Category $category): bool => $category->items_count > 0)
            ->map(fn (Category $category): array => ['label' => $category->name, 'other' => false, 'count' => $category->items_count])
            ->sortByDesc('count')
            ->values();

        $rows = $categories->take(self::NAMED_CATEGORIES)->all();
        $tail = $categories->skip(self::NAMED_CATEGORIES);

        if ($tail->isNotEmpty()) {
            $rows[] = ['label' => null, 'other' => true, 'count' => (int) $tail->sum('count')];
        }

        $uncategorised = $this->collection->items()->whereNull('category_id')->count();

        if ($uncategorised > 0) {
            $rows[] = ['label' => null, 'other' => false, 'count' => $uncategorised];
        }

        $total = array_sum(array_column($rows, 'count'));

        return array_map(fn (array $row): array => [
            'label' => $row['label'],
            'other' => $row['other'],
            'count' => $row['count'],
            'percentage' => $this->percentage($row['count'], $total),
        ], $rows);
    }

    /**
     * Every category of the collection with what it holds, biggest first. Unlike
     * byCategory(), which folds the tail into an "Other" slice for the chart,
     * this keeps each category whole, so a category page can say where it sits
     * among its siblings.
     *
     * @return list<array{id: int, name: string, count: int, value: int}>
     */
    public function categoryBreakdown(): array
    {
        $values = DB::query()
            ->fromSub($this->valued(), 'valued')
            ->whereNotNull('valued.category_id')
            ->groupBy('valued.category_id')
            ->selectRaw('valued.category_id as category_id, sum(valued.value) as total')
            ->pluck('total', 'category_id');

        return $this->collection->categories()
            ->withCount('items')
            ->get()
            ->map(fn (Category $category): array => [
                'id' => $category->id,
                'name' => $category->name,
                'count' => $category->items_count,
                'value' => (int) ($values[$category->id] ?? 0),
            ])
            ->sortByDesc('count')
            ->values()
            ->all();
    }

    /**
     * The condition the copies are in.
     *
     * @return list<array{label: ?string, count: int, percentage: int}>
     */
    public function byCondition(): array
    {
        $rows = DB::query()
            ->fromSub($this->valued(), 'valued')
            ->selectRaw('valued.condition_id as condition_id, count(*) as total')
            ->groupBy('valued.condition_id')
            ->get();

        $names = Condition::query()->whereIn('id', $rows->pluck('condition_id')->filter())->get()->keyBy('id');

        $total = (int) $rows->sum('total');

        return $rows
            ->map(fn (object $row): array => [
                'label' => $names->get($row->condition_id)?->name,
                'count' => (int) $row->total,
            ])
            ->sortByDesc('count')
            ->map(fn (array $row): array => [
                ...$row,
                'percentage' => $this->percentage($row['count'], $total),
            ])
            ->values()
            ->all();
    }

    /**
     * Where the value of the collection physically sits.
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
            ->values()
            ->all();
    }

    /**
     * The most valuable items, worth the sum of their copies. The condition and
     * the location shown are those of the single copy carrying the most value,
     * as an item with several copies has no one answer.
     *
     * @return list<array{item: Item, value: int, condition: ?string, location: ?string}>
     */
    public function topItems(): array
    {
        $totals = DB::query()
            ->fromSub($this->valued(), 'valued')
            ->selectRaw('valued.item_id as item_id, sum(valued.value) as total')
            ->groupBy('valued.item_id')
            ->having('total', '>', 0)
            ->orderByDesc('total')
            ->limit(self::TOP_ITEMS)
            ->pluck('total', 'item_id');

        if ($totals->isEmpty()) {
            return [];
        }

        $items = Item::query()
            ->whereIn('id', $totals->keys())
            ->with(['copies.condition', 'copies.currentLocation', 'copies.latestValuation'])
            ->get()
            ->keyBy('id');

        return $totals
            ->map(function (int|string $total, int|string $itemId) use ($items): ?array {
                $item = $items->get((int) $itemId);

                if (! $item instanceof Item) {
                    return null;
                }

                $copy = $item->copies->sortByDesc(fn (Copy $copy): int => $copy->estimatedValue() ?? 0)->first();

                return [
                    'item' => $item,
                    'value' => (int) $total,
                    'condition' => $copy?->condition?->name,
                    'location' => $copy?->currentLocation?->name,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Every copy of the collection, with what it is currently worth.
     *
     * A copy carries no value of its own any more, so the figure is the amount
     * of its most recent valuation, or zero when it has never been valued. The
     * id breaks ties on the date, so a copy valued twice in one day reads the
     * second of the two rather than picking arbitrarily.
     *
     * The category comes along because the category breakdown groups by it, and
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
            ->where('items.collection_id', $this->collection->id)
            ->whereNull('items.deleted_at')
            ->whereNull('copies.deleted_at')
            ->select([
                'copies.id',
                'copies.item_id',
                'copies.condition_id',
                'copies.current_location_id',
                'items.category_id',
            ])
            ->selectSub(
                DB::query()->selectRaw('coalesce(('.$latest->toSql().'), 0)')->mergeBindings($latest),
                'value',
            );
    }

    /**
     * When each copy of the collection was acquired, keyed by copy id.
     *
     * The acquisition date left the copy with the restructuring (#117): it is
     * read from the transaction that acquired the copy, and transactions arrive
     * with #118. Until then nothing carries an acquisition date, so the two
     * charts over time render empty and every copy counts as undated.
     *
     * This is deliberately the only place that answers the question, so wiring
     * it to the transactions is a change to one method rather than to four.
     *
     * @return Collection<int, Carbon>
     */
    private function acquisitionDates(): Collection
    {
        return collect();
    }

    /**
     * The first day of each month of the window, oldest first.
     *
     * @return list<Carbon>
     */
    private function months(): array
    {
        $start = $this->windowStart();

        return array_map(fn (int $offset): Carbon => $start->copy()->addMonths($offset), range(0, self::MONTHS - 1));
    }

    private function windowStart(): Carbon
    {
        return Carbon::now()->startOfMonth()->subMonths(self::MONTHS - 1);
    }

    /**
     * What share of the whole a row stands for, rounded to a whole percent.
     */
    private function percentage(int $count, int $total): int
    {
        return $total === 0 ? 0 : (int) round(($count / $total) * 100);
    }
}
