<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Actions\IndexItemPhotoSearchTokens;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Rebuild the search hashes of every photo of an item.
 *
 * A photo is searchable by the name of its item as well as by its own file
 * name, so renaming an item leaves its photos indexed under the old name until
 * this runs.
 */
class ReindexItemPhotos implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Item $item,
    ) {}

    public function handle(): void
    {
        $this->item->photos()->with('item')->chunkById(100, function ($photos): void {
            $photos->each(fn (ItemPhoto $photo) => new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute());
        });
    }
}
