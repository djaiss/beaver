<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Stamps who soft deleted a record, so the trash screen can say who put it there.
 * Like HasAuthor we keep both the user id (nullable, since users can be deleted)
 * and a name snapshot that survives the deletion. Restoring clears both.
 */
trait HasDeleter
{
    public static function bootHasDeleter(): void
    {
        static::deleting(function (self $model): void {
            $user = Auth::user();

            if (! $user instanceof User) {
                return;
            }

            $model->deleted_by_id = $user->id;
            $model->deleted_by_name = $user->getFullName();
            $model->saveQuietly();
        });

        static::restoring(function (self $model): void {
            $model->deleted_by_id = null;
            $model->deleted_by_name = null;
        });
    }

    public function initializeHasDeleter(): void
    {
        $this->mergeCasts([
            'deleted_by_name' => 'encrypted',
        ]);
    }

    /**
     * Get the user who soft deleted the record.
     *
     * @return BelongsTo<User, $this>
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by_id');
    }
}
