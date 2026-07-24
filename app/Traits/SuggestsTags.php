<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Shared by the controllers behind the item screens, which all offer the tags
 * already in use in the account when tagging an item.
 */
trait SuggestsTags
{
    /**
     * A tag name is encrypted, so the sorting cannot happen in the database.
     *
     * @return Collection<int, Tag>
     */
    private function accountTags(Request $request): Collection
    {
        return $request->user()->account->tags()
            ->get()
            ->sortBy(fn (Tag $tag): string => mb_strtolower($tag->name))
            ->values();
    }
}
