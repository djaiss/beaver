<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete an entire account. Cascades to its members, invitations and everything
 * the account owns. Only an owner may do so.
 */
class DestroyAccount
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->account->delete();
    }

    private function validate(): void
    {
        if ($this->account->roleFor($this->user) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AccountDeletion,
            parameters: ['name' => $this->account->name],
        )->onQueue('low');
    }
}
