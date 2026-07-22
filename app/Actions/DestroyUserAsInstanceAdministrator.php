<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Delete any single user on the instance. Distinct from DestroyUser, which is
 * how someone closes their own user and records why they left.
 */
class DestroyUserAsInstanceAdministrator
{
    public function __construct(
        private readonly User $user,
        private readonly User $userToDelete,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->userToDelete->delete();
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('User not found');
        }

        // Deleting yourself from here would end the session mid request.
        if ($this->user->id === $this->userToDelete->id) {
            throw new ModelNotFoundException('User not found');
        }

        // Same rule as removing a member from inside the account: taking the last
        // owner away would leave everyone else unable to administer it, and there
        // is no way to promote someone back.
        if ($this->isLastOwner()) {
            throw ValidationException::withMessages(['user' => 'You cannot delete the last owner of an account']);
        }
    }

    private function isLastOwner(): bool
    {
        if ($this->userToDelete->role !== PermissionEnum::Owner->value) {
            return false;
        }

        return User::query()
            ->where('account_id', $this->userToDelete->account_id)
            ->where('role', PermissionEnum::Owner->value)
            ->count() === 1;
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::InstanceUserDeletion,
            parameters: ['email' => $this->userToDelete->email],
        )->onQueue('low');
    }
}
