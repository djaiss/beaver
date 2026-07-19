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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * The numbers behind the statistics screen of a collection.
 *
 * Everything is read from the copies rather than the items, because a copy is
 * what carries the money, the condition and the location. Three copies of the
 * same comic are three amounts, not one.
 *
 * The two charts that run over time key off `acquired_at`, which is optional.
 * Copies without one are left out of those two rather than being parked in an
 * arbitrary month, so the screen reports how many were skipped.
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
        $value = (int) $this->copies()->sum('estimated_value');
        $startOfMonth = Carbon::now()->startOfMonth();

        return [
            'items' => $items,
            'copies' => $this->copies()->count(),
            'value' => $value,
            'average' => $items === 0 ? 0 : (int) round($value / $items),
            'itemsAddedThisMonth' => $this->collection->items()->where('created_at', '>=', $startOfMonth)->count(),
            'valueAddedThisMonth' => (int) $this->copies()->where('acquired_at', '>=', $startOfMonth)->sum('estimated_value'),
            'undatedCopies' => $this->copies()->whereNull('acquired_at')->count(),
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

        $monthly = $this->datedCopies()
            ->where('acquired_at', '>=', $start)
            ->get(['acquired_at', 'estimated_value'])
            ->groupBy(fn (Copy $copy): string => $copy->acquired_at->format('Y-m'))
            ->map(fn (Collection $copies): int => (int) $copies->sum('estimated_value'));

        $running = (int) $this->datedCopies()->where('acquired_at', '<', $start)->sum('estimated_value');

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
        $monthly = $this->datedCopies()
            ->where('acquired_at', '>=', $this->windowStart())
            ->get(['acquired_at'])
            ->groupBy(fn (Copy $copy): string => $copy->acquired_at->format('Y-m'))
            ->map(fn (Collection $copies): int => $copies->count());

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
        $values = Copy::query()
            ->join('items', 'items.id', '=', 'copies.item_id')
            ->where('items.collection_id', $this->collection->id)
            ->whereNotNull('items.category_id')
            ->groupBy('items.category_id')
            ->selectRaw('items.category_id as category_id, sum(copies.estimated_value) as total')
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
        $rows = $this->copies()
            ->selectRaw('condition_id, count(*) as total')
            ->groupBy('condition_id')
            ->get();

        $names = Condition::query()->whereIn('id', $rows->pluck('condition_id')->filter())->get()->keyBy('id');

        $total = $this->copies()->count();

        return $rows
            ->map(fn (Copy $row): array => [
                'label' => $names->get($row->condition_id)?->name,
                'count' => (int) $row->getAttribute('total'),
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
        $rows = $this->copies()
            ->selectRaw('location_id, sum(estimated_value) as total')
            ->groupBy('location_id')
            ->get();

        $names = Location::query()->whereIn('id', $rows->pluck('location_id')->filter())->get()->keyBy('id');

        return $rows
            ->map(fn (Copy $row): array => [
                'label' => $names->get($row->location_id)?->name,
                'value' => (int) $row->getAttribute('total'),
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
        return $this->collection->items()
            ->withSum('copies', 'estimated_value')
            ->with(['copies.condition', 'copies.location'])
            ->orderByDesc('copies_sum_estimated_value')
            ->limit(self::TOP_ITEMS)
            ->get()
            ->filter(fn (Item $item): bool => (int) $item->getAttribute('copies_sum_estimated_value') > 0)
            ->map(function (Item $item): array {
                $copy = $item->copies->sortByDesc('estimated_value')->first();

                return [
                    'item' => $item,
                    'value' => (int) $item->getAttribute('copies_sum_estimated_value'),
                    'condition' => $copy?->condition?->name,
                    'location' => $copy?->location?->name,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return Builder<Copy>
     */
    private function copies(): Builder
    {
        return Copy::query()->whereIn('item_id', $this->collection->items()->select('items.id'));
    }

    /**
     * @return Builder<Copy>
     */
    private function datedCopies(): Builder
    {
        return $this->copies()->whereNotNull('acquired_at');
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
