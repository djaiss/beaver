<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\AccountMember;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Claim a pending invitation and add the user to the account.
 */
class AcceptInvitation
{
    private AccountMember $member;

    public function __construct(
        private readonly Invitation $invitation,
        private readonly User $user,
    ) {}

    public function execute(): AccountMember
    {
        $this->validate();
        $this->join();
        $this->markAccepted();
        $this->log();

        return $this->member;
    }

    private function validate(): void
    {
        if (! $this->invitation->isPending()) {
            throw ValidationException::withMessages(['token' => 'This invitation is no longer valid']);
        }

        if (mb_strtolower($this->user->email) !== mb_strtolower($this->invitation->email)) {
            throw ValidationException::withMessages(['token' => 'This invitation was sent to a different email address']);
        }

        if ($this->invitation->account->hasMember($this->user)) {
            throw ValidationException::withMessages(['token' => 'You are already a member of this account']);
        }
    }

    private function join(): void
    {
        $this->member = new AddAccountMember(
            account: $this->invitation->account,
            user: $this->user,
            role: $this->invitation->role,
            invitedBy: $this->invitation->invitedBy,
        )->execute();
    }

    private function markAccepted(): void
    {
        $this->invitation->accepted_at = now();
        $this->invitation->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::InvitationAccepted,
            parameters: ['name' => $this->invitation->account->name],
        )->onQueue('low');
    }
}
