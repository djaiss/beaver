<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The roadmap tab of an item, listing what an item will eventually track.
 * Reading an item is open to any role.
 */
class ItemRoadmapController extends Controller
{
    use FindsItems;

    public function index(Request $request, int $collection, int $item): View
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, [
            'tags',
            'copies',
            'category',
            'collectionType',
        ]);

        return view('app.items.roadmap', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'tags' => $this->accountTags($request),
        ]);
    }
}
