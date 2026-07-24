<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a piece of work performed on a copy. Only owners and editors of its
 * account may do so.
 *
 * The provenance event the record generated was only there because of it, so it
 * goes with the record rather than being left orphaned in the object's story.
 */
class DestroyMaintenanceRecord
{
    public function __construct(
        private readonly User $user,
        private readonly MaintenanceRecord $record,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->record->provenanceEvent?->delete();
        $this->record->delete();
    }

    private function validate(): void
    {
        $account = $this->record->copy->item->catalog->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    /**
     * Log before the row goes, so the entry can still describe what was deleted.
     */
    private function log(): void
    {
        $item = $this->record->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::MaintenanceRecordDeletion,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::MaintenanceRecordDeletion,
            parameters: ['label' => $this->record->type->label()],
        )->onQueue('low');
    }
}
