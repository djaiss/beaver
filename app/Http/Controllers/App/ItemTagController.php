<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\AttachTagToItem;
use App\Actions\DetachTagFromItem;
use App\Http\Controllers\Controller;
use App\Models\Collection as CollectionModel;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemTagController extends Controller
{
    public function create(Request $request): RedirectResponse
    {
        $collection = $request->attributes->get('collection');
        $item = $request->attributes->get('item');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new AttachTagToItem(
            user: $request->user(),
            item: $item,
            name: $validated['name'],
        )->execute();

        return to_route('items.show', [$collection, $item])
            ->with('status', __('Tag added'))
            ->with('status_description', __('The tag is now on this item.'));
    }

    public function destroy(Request $request, CollectionModel $collection, Item $item, int $tag): RedirectResponse
    {
        try {
            $tagModel = $request->user()->account->tags()->findOrFail($tag);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DetachTagFromItem(
            user: $request->user(),
            item: $item,
            tag: $tagModel,
        )->execute();

        return to_route('items.show', [$collection, $item])
            ->with('status', __('Tag removed'))
            ->with('status_description', __('The tag is no longer on this item, and stays available for other items.'));
    }
}
