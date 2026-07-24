<?php

declare(strict_types=1);

namespace App\Traits;

use App\Actions\IndexSearchable;
use App\Jobs\ReindexSearchDependents;
use App\Models\SearchToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Keeps a record's rows in the account wide search index in step with the
 * record itself.
 *
 * Saving reindexes, deleting clears. Deleting covers a soft delete as well, so
 * something in the trash stops turning up in search until it is restored, and
 * force deleting goes through the same event so nothing is left orphaned.
 *
 * Renaming is the one case a record cannot answer for on its own: the name of a
 * collection is indexed into each of its items, so changing it leaves them
 * quoting the old one. That is handed to a queued job rather than done inline,
 * which means search can lag a rename by as long as the queue takes.
 */
trait HasSearchIndex
{
    public static function bootHasSearchIndex(): void
    {
        static::saved(function ($model): void {
            new IndexSearchable(searchable: $model)->execute();
        });

        static::updated(function ($model): void {
            if (! $model->wasChanged($model->searchableTitleColumn())) {
                return;
            }

            ReindexSearchDependents::dispatch($model)->onQueue('low');
        });

        static::deleted(function ($model): void {
            $model->searchTokens()->delete();
        });

        // Registered through the low level helper rather than static::restored(),
        // which only exists on the models that soft delete.
        static::registerModelEvent('restored', function ($model): void {
            new IndexSearchable(searchable: $model)->execute();
        });
    }

    /**
     * Get the search hashes of the record.
     *
     * @return MorphMany<SearchToken, $this>
     */
    public function searchTokens(): MorphMany
    {
        return $this->morphMany(SearchToken::class, 'searchable');
    }

    /**
     * The column whose change makes the records quoting this one go stale.
     */
    public function searchableTitleColumn(): string
    {
        return 'name';
    }

    public function searchableThumbnailUrl(): ?string
    {
        return null;
    }

    public function searchableCollectionName(): ?string
    {
        return null;
    }

    /**
     * @return iterable<int, Model>
     */
    public function searchableDependents(): iterable
    {
        return [];
    }
}
