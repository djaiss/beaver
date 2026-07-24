<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Copy;
use App\Services\CatalogStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The item list of a collection is served from two places: the collection itself,
 * and one of its categories. Both render the same screen, the category one over a
 * narrower set of items, so the query and the view data live here.
 */
trait ShowsCatalogItems
{
    /**
     * Searching and filtering the items happens in the browser, over the rows of
     * the current page only, so the page holds as many items as it reasonably can.
     */
    private const int ITEMS_PER_PAGE = 1000;

    private function catalogItemsView(Request $request, Catalog $catalog, ?Category $category = null): View
    {
        // Only the category page shows the panel that needs these, so the plain
        // collection page does not pay for the extra queries.
        $statistics = $category === null ? null : new CatalogStatistics(catalog: $catalog);

        $query = fn () => $catalog->items()
            ->when($category, fn ($items) => $items->where('category_id', $category->id));

        $items = $query()
            ->with(['mainPhoto', 'copies.itemCondition', 'copies.currentLocation', 'copies.latestValuation'])
            ->orderByDesc('id')
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        return view('app.catalogs.show', [
            'category' => $category,
            'view' => $catalog->viewForUser($request->user()),
            'items' => $items,
            'itemCount' => $query()->count(),
            // A copy carries no value of its own any more, so the total is the
            // sum of what each was last valued at. The valuations are eager
            // loaded rather than summed in SQL, which would mean a correlated
            // subquery per row to find the latest one.
            'totalValue' => (int) Copy::whereIn('item_id', $query()->select('id'))
                ->with('latestValuation')
                ->get()
                ->sum(fn (Copy $copy): int => $copy->estimatedValue() ?? 0),
            'categoryBreakdown' => $statistics?->categoryBreakdown() ?? [],
            'catalogTotals' => $statistics?->totals(),
        ]);
    }
}
