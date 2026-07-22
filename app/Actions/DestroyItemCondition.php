<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\ItemCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a condition. Only owners and editors of its account may do so;
 * system default conditions cannot be deleted.
 */
class DestroyItemCondition
{
    public function __construct(
        private readonly User $user,
        private readonly ItemCondition $itemCondition,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->itemCondition->delete();
    }

    private function validate(): void
    {
        if ($this->itemCondition->isSystemDefault()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->itemCondition->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionDeletion,
            parameters: ['name' => $this->itemCondition->name],
        )->onQueue('low');
    }
}
