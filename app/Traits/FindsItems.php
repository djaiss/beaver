<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Collection;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Shared by the controllers behind the item screens. Each tab of an item is its
 * own page, so they all resolve the same collection and item first, and all
 * render the same shell around their panel.
 */
trait FindsItems
{
    private function findCollection(Request $request, int $collection): Collection
    {
        try {
            return $request->user()->account->collections()->findOrFail($collection);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * The shell of every item page reads the tags and counts the copies, so
     * those are always loaded. A page adds whatever its own panel needs.
     *
     * @param  list<string>|array<string, callable>  $with
     */
    private function findItem(Collection $collection, int $item, array $with = ['tags', 'copies', 'customFieldValues', 'photos', 'mainPhoto']): Item
    {
        try {
            return $collection->items()->with($with)->findOrFail($item);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * The tags of the account, offered as suggestions when tagging the item.
     * A tag name is encrypted, so the sorting cannot happen in the database.
     *
     * @return SupportCollection<int, Tag>
     */
    private function accountTags(Request $request): SupportCollection
    {
        return $request->user()->account->tags()
            ->get()
            ->sortBy(fn (Tag $tag): string => mb_strtolower($tag->name))
            ->values();
    }
}
