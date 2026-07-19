<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\IndexItemPhotoSearchTokens;
use App\Models\ItemPhoto;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Rebuilds what the photos screen needs but cannot infer at read time: the search
 * hashes of every photo, and the pixel dimensions of the photos uploaded before
 * they were recorded.
 *
 * Safe to run again at any time. It is how an existing installation catches up
 * after upgrading, and how a search index is repaired if it ever drifts.
 */
class RebuildPhotoSearchIndexCommand extends Command
{
    protected $signature = 'photos:rebuild-search-index';

    protected $description = 'Rebuild the search index and backfill the dimensions of every photo';

    public function handle(): int
    {
        $indexed = 0;
        $measured = 0;

        ItemPhoto::query()->with('item')->chunkById(100, function ($photos) use (&$indexed, &$measured): void {
            foreach ($photos as $photo) {
                $this->line('Indexing photo '.$photo->id.'…');

                if ($this->measure($photo)) {
                    $measured++;
                }

                new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();
                $indexed++;
            }
        });

        $this->info($indexed.' photo(s) indexed, '.$measured.' backfilled with dimensions.');

        return self::SUCCESS;
    }

    /**
     * Read the pixel size off the stored file, for photos that have none. A file
     * that has gone missing from the disk is left alone rather than failing the
     * whole run.
     */
    private function measure(ItemPhoto $photo): bool
    {
        if ($photo->width !== null && $photo->height !== null) {
            return false;
        }

        $disk = Storage::disk((string) config('filesystems.default'));

        if (! $disk->exists($photo->path)) {
            return false;
        }

        $size = @getimagesizefromstring((string) $disk->get($photo->path));

        if ($size === false) {
            return false;
        }

        $photo->width = (int) $size[0];
        $photo->height = (int) $size[1];
        $photo->save();

        return true;
    }
}
