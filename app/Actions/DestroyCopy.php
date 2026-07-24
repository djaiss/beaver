<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Copy;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Soft delete a copy of an item. Only owners and editors of its account may do
 * so.
 */
class DestroyCopy
{
    public function __construct(
        private readonly User $user,
        private readonly Copy $copy,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->copy->delete();
    }

    private function validate(): void
    {
        if (! $this->copy->item->catalog->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CopyDeletion,
            parameters: ['name' => $this->copy->item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $this->copy->item,
            user: $this->user,
            action: ItemActionEnum::CopyDeletion,
        )->onQueue('low');
    }
}
