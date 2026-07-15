<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a condition. Only owners and editors of its account may do so;
 * system default conditions cannot be deleted.
 */
class DestroyCondition
{
    public function __construct(
        private readonly User $user,
        private readonly Condition $condition,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->condition->delete();
    }

    private function validate(): void
    {
        if ($this->condition->isSystemDefault()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->condition->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionDeletion,
            parameters: ['name' => $this->condition->name],
        )->onQueue('low');
    }
}
