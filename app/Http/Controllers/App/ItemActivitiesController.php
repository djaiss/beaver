<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The activity tab of an item, showing everything done to it. Reading an item
 * is open to any role.
 */
class ItemActivitiesController extends Controller
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

        return view('app.items.activities', [
            'collection' => $collectionModel,
            'item' => $itemModel,
            'tags' => $this->accountTags($request),
            // Newest first. The user is eager loaded because every entry reads
            // the current name of whoever performed the action.
            'activity' => $itemModel->logs()->with('user')->latest()->get(),
        ]);
    }
}
