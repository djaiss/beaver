<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Copy;
use App\Services\CollectionStatistics;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The item list of a collection is served from two places: the collection itself,
 * and one of its categories. Both render the same screen, the category one over a
 * narrower set of items, so the query and the view data live here.
 */
trait ShowsCollectionItems
{
    /**
     * Searching and filtering the items happens in the browser, over the rows of
     * the current page only, so the page holds as many items as it reasonably can.
     */
    private const int ITEMS_PER_PAGE = 1000;

    private function collectionItemsView(Request $request, Collection $collection, ?Category $category = null): View
    {
        // Only the category page shows the panel that needs these, so the plain
        // collection page does not pay for the extra queries.
        $statistics = $category === null ? null : new CollectionStatistics(collection: $collection);

        $query = fn () => $collection->items()
            ->when($category, fn ($items) => $items->where('category_id', $category->id));

        $items = $query()
            ->with(['mainPhoto', 'copies.condition', 'copies.location'])
            ->orderByDesc('id')
            ->paginate(self::ITEMS_PER_PAGE)
            ->withQueryString();

        return view('app.collections.show', [
            'collection' => $collection,
            'category' => $category,
            'view' => $collection->viewForUser($request->user()),
            'items' => $items,
            'itemCount' => $query()->count(),
            'totalValue' => (int) Copy::whereIn('item_id', $query()->select('id'))->sum('estimated_value'),
            'categoryBreakdown' => $statistics?->categoryBreakdown() ?? [],
            'collectionTotals' => $statistics?->totals(),
        ]);
    }
}
