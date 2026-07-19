<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The history tab of an item. Reading an item is open to any role.
 *
 * The tab is the chronological view across everything hanging off a copy. Most
 * of what it will read from does not exist yet, so for now it shows the sections
 * it will hold and fills only the valuations, which are the one history the copy
 * restructuring brought with it.
 */
class ItemHistoryController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection, int $item): View
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, [
            'tags',
            'copies.condition',
            'copies.currentLocation',
            'copies.valuations',
            'category',
            'collectionType',
        ]);

        return view('app.items.history', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'tags' => $this->accountTags($request),
        ]);
    }
}
