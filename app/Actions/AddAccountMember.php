<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\AccountMember;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Add a user to an account with a given role.
 */
class AddAccountMember
{
    private AccountMember $member;

    public function __construct(
        private readonly Account $account,
        private readonly User $user,
        private string $role = PermissionEnum::Viewer->value,
        private readonly ?User $invitedBy = null,
    ) {}

    public function execute(): AccountMember
    {
        $this->validate();
        $this->create();

        return $this->member;
    }

    private function validate(): void
    {
        if (PermissionEnum::tryFrom($this->role) === null) {
            throw ValidationException::withMessages(['role' => 'Invalid role']);
        }
    }

    private function create(): void
    {
        $this->member = AccountMember::query()->create([
            'account_id' => $this->account->id,
            'user_id' => $this->user->id,
            'role' => $this->role,
            'invited_by' => $this->invitedBy?->id,
            'joined_at' => now(),
        ]);
    }
}
