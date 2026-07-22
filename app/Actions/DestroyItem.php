<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Soft delete an item. Only owners and editors of its account may do so.
 */
class DestroyItem
{
    public function __construct(
        private readonly User $user,
        private readonly Item $item,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->item->delete();
    }

    private function validate(): void
    {
        if (! $this->item->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemDeletion,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');
    }
}
