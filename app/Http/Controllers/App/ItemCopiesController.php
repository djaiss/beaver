<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Traits\SuggestsTags;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The copies tab of an item. Reading an item is open to any role.
 */
class ItemCopiesController extends Controller
{
    use SuggestsTags;

    public function index(Request $request): View
    {
        $request->attributes->get('item')->load([
            'tags',
            'copies.itemCondition',
            'copies.currentLocation',
            'copies.openLocationHistory',
            'copies.latestValuation',
            'copies.acquiringTransaction',
            'category',
            'catalogType',
        ]);

        return view('app.items.copies', [
            'tags' => $this->accountTags($request),
        ]);
    }
}
