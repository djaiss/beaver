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
 * Change a member's role. Only an owner may do so, and the account must always
 * keep at least one owner.
 */
class UpdateMemberRole
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private readonly User $member,
        private string $role,
    ) {}

    public function execute(): User
    {
        $this->validate();
        $this->update();
        $this->log();

        return $this->member;
    }

    private function validate(): void
    {
        if ($this->account->roleFor($this->user) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->member->account_id !== $this->account->id) {
            throw new ModelNotFoundException('Member not found');
        }

        if (PermissionEnum::tryFrom($this->role) === null) {
            throw ValidationException::withMessages(['role' => 'Invalid role']);
        }

        if ($this->wouldRemoveLastOwner()) {
            throw ValidationException::withMessages(['role' => 'The account must have at least one owner']);
        }
    }

    private function wouldRemoveLastOwner(): bool
    {
        if ($this->member->role !== PermissionEnum::Owner->value) {
            return false;
        }

        if ($this->role === PermissionEnum::Owner->value) {
            return false;
        }

        return $this->account->users()->where('role', PermissionEnum::Owner->value)->count() === 1;
    }

    private function update(): void
    {
        $this->member->role = $this->role;
        $this->member->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::MemberRoleUpdated,
            parameters: ['role' => $this->role],
        )->onQueue('low');
    }
}
