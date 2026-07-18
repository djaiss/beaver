<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\TrashableEnum;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Queue\Queueable;

class PurgeTrash implements ShouldQueue
{
    use Queueable;

    /**
     * Permanently delete everything that has sat in the trash past the retention
     * window. This is what makes the promise on the trash screen true.
     */
    public function handle(): void
    {
        $cutoff = now()->subDays(config('trash.retention_days'));

        foreach (TrashableEnum::cases() as $type) {
            $modelClass = $type->modelClass();

            $modelClass::onlyTrashed()
                ->where('deleted_at', '<=', $cutoff)
                ->each(fn (Model $model): ?bool => $model->forceDelete());
        }
    }
}
