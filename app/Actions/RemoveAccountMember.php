<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Remove a member from an account by deleting their user. Only an owner may do
 * so, and the last owner cannot be removed.
 */
class RemoveAccountMember
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private readonly User $member,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->member->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->account->roleFor($this->user) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->member->account_id !== $this->account->id) {
            throw new ModelNotFoundException('Member not found');
        }

        if ($this->isLastOwner()) {
            throw ValidationException::withMessages(['member' => 'You cannot remove the last owner of the account']);
        }
    }

    private function isLastOwner(): bool
    {
        if ($this->member->role !== PermissionEnum::Owner->value) {
            return false;
        }

        return $this->account->users()->where('role', PermissionEnum::Owner->value)->count() === 1;
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::MemberRemoved,
        )->onQueue('low');
    }
}
