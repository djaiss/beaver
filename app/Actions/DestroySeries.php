<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Item;
use App\Models\Series;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a series. Only owners and editors of its account may do so.
 *
 * The items keep their data: the foreign key unlinks them rather than taking
 * them down with the series.
 */
class DestroySeries
{
    public function __construct(
        private readonly User $user,
        private readonly Series $series,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->unlinkItems();
        $this->series->delete();
    }

    /**
     * The foreign key would do this, but only where the driver enforces it, and the sqlite
     * connection the suite runs on does not. Unlinking here makes the behaviour the same
     * everywhere. Trashed items are included so restoring one does not bring back a dangling
     * series id.
     */
    private function unlinkItems(): void
    {
        Item::withTrashed()
            ->where('series_id', $this->series->id)
            ->update(['series_id' => null]);
    }

    private function validate(): void
    {
        if (! $this->series->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SeriesDeletion,
            parameters: ['name' => $this->series->name],
        )->onQueue('low');
    }
}
