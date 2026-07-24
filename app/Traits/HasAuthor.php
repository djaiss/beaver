<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

/**
 * Stamps who created and last updated a shared record. We keep both the user id
 * (nullable, since users can be deleted) and a name snapshot that survives the
 * deletion. When no author is provided, we fall back to the authenticated user.
 */
trait HasAuthor
{
    public static function bootHasAuthor(): void
    {
        static::creating(function (self $model): void {
            $model->stampAuthorWhenMissing('created_by');
            $model->stampAuthorWhenMissing('updated_by');
        });

        static::updating(function (self $model): void {
            $model->stampAuthorFromAuth('updated_by');
        });
    }

    public function initializeHasAuthor(): void
    {
        $this->mergeCasts([
            'created_by_name' => 'encrypted',
            'updated_by_name' => 'encrypted',
        ]);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }

    private function stampAuthorWhenMissing(string $prefix): void
    {
        if ($this->getAttribute($prefix.'_id') !== null) {
            return;
        }

        $this->stampAuthorFromAuth($prefix);
    }

    private function stampAuthorFromAuth(string $prefix): void
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            return;
        }

        $this->setAttribute($prefix.'_id', $user->id);
        $this->setAttribute($prefix.'_name', $user->getFullName());
    }
}
