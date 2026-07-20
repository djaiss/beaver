<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Document;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

/**
 * Gives a record the documents attached to it, and the retention policy that
 * removes them with it.
 *
 * A document belongs to exactly one record, so it is never shared and a file is
 * never needed elsewhere. The policy is therefore simple: when a record is
 * permanently deleted, its documents and their files go with it. A soft deleted
 * record keeps its documents, so restoring it brings them back. Deletions that
 * cascade at the database level bypass model events and are out of scope here,
 * the same way item photo files are.
 */
trait HasDocuments
{
    public static function bootHasDocuments(): void
    {
        static::deleting(function ($model): void {
            if (method_exists($model, 'isForceDeleting') && ! $model->isForceDeleting()) {
                return;
            }

            $model->purgeDocuments();
        });
    }

    /**
     * Get the documents attached to the record, newest issue first.
     *
     * @return MorphMany<Document, $this>
     */
    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable')
            ->orderByDesc('issued_at')
            ->orderByDesc('id');
    }

    /**
     * Remove the documents attached to the record, deleting each stored file from
     * the disk before its row. A file cannot be rolled back, so it goes once the
     * row it belongs to is on its way out.
     */
    public function purgeDocuments(): void
    {
        $this->documents()->get()->each(function (Document $document): void {
            if ($document->path !== null) {
                Storage::disk((string) config('filesystems.default'))->delete($document->path);
            }

            $document->delete();
        });
    }
}
