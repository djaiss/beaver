<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\AttachTagToItem;
use App\Actions\DetachTagFromItem;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemTagController extends Controller
{
    public function create(Request $request, int $collection, int $item): RedirectResponse
    {
        $itemModel = $this->findItem($request, $collection, $item);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        new AttachTagToItem(
            user: $request->user(),
            item: $itemModel,
            name: $validated['name'],
        )->execute();

        return to_route('items.show', [$collection, $item])
            ->with('status', __('Tag added'))
            ->with('status_description', __('The tag is now on this item.'));
    }

    public function destroy(Request $request, int $collection, int $item, int $tag): RedirectResponse
    {
        $account = $request->user()->account;

        $itemModel = $this->findItem($request, $collection, $item);

        try {
            $tagModel = $account->tags()->findOrFail($tag);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DetachTagFromItem(
            user: $request->user(),
            item: $itemModel,
            tag: $tagModel,
        )->execute();

        return to_route('items.show', [$collection, $item])
            ->with('status', __('Tag removed'))
            ->with('status_description', __('The tag is no longer on this item, and stays available for other items.'));
    }

    private function findItem(Request $request, int $collection, int $item): Item
    {
        try {
            return $request->user()->account->collections()
                ->findOrFail($collection)
                ->items()
                ->findOrFail($item);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
