<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Traits\SuggestsTags;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * The roadmap tab of an item, listing what an item will eventually track.
 * Reading an item is open to any role.
 */
class ItemRoadmapController extends Controller
{
    use SuggestsTags;

    public function index(Request $request): View
    {
        $request->attributes->get('item')->load([
            'tags',
            'copies',
            'category',
            'catalogType',
        ]);

        return view('app.items.roadmap', [
            'tags' => $this->accountTags($request),
        ]);
    }
}
