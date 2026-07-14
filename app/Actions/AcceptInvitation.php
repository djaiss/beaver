<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Mark a pending invitation as accepted. The user has already been created in
 * the inviting account (a user always belongs to exactly one account), so this
 * only closes out the invitation.
 */
class AcceptInvitation
{
    public function __construct(
        private readonly Invitation $invitation,
        private readonly User $user,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->markAccepted();
        $this->log();
    }

    private function validate(): void
    {
        if (! $this->invitation->isPending()) {
            throw ValidationException::withMessages(['token' => 'This invitation is no longer valid']);
        }

        if (mb_strtolower($this->user->email) !== mb_strtolower($this->invitation->email)) {
            throw ValidationException::withMessages(['token' => 'This invitation was sent to a different email address']);
        }
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
