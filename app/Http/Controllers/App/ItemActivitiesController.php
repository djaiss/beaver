<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Traits\SuggestsTags;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The activity tab of an item, showing everything done to it. Reading an item
 * is open to any role.
 */
class ItemActivitiesController extends Controller
{
    use SuggestsTags;

    public function index(Request $request): View
    {
        $item = $request->attributes->get('item');
        $item->load([
            'tags',
            'copies',
            'category',
            'collectionType',
        ]);

        return view('app.items.activities', [
            'tags' => $this->accountTags($request),
            // Newest first. The user is eager loaded because every entry reads
            // the current name of whoever performed the action.
            'activity' => $item->logs()->with('user')->latest()->get(),
        ]);
    }
}
