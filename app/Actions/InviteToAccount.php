<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Mail\AccountInvitation;
use App\Models\Account;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Invite a person to join an account by email. Only an owner may do so.
 */
class InviteToAccount
{
    private Invitation $invitation;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $email,
        private string $role = PermissionEnum::Viewer->value,
    ) {}

    public function execute(): Invitation
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->sendEmail();
        $this->log();

        return $this->invitation;
    }

    private function sanitize(): void
    {
        $this->email = mb_strtolower(TextSanitizer::plainText($this->email));
    }

    private function validate(): void
    {
        if ($this->account->roleFor($this->user) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }

        if (PermissionEnum::tryFrom($this->role) === null) {
            throw ValidationException::withMessages(['role' => 'Invalid role']);
        }

        if ($this->account->users()->where('email', $this->email)->exists()) {
            throw ValidationException::withMessages(['email' => 'This person is already a member of the account']);
        }

        $alreadyInvited = $this->account->invitations()
            ->where('email', $this->email)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists();

        if ($alreadyInvited) {
            throw ValidationException::withMessages(['email' => 'This person has already been invited']);
        }
    }

    private function create(): void
    {
        $this->invitation = Invitation::query()->create([
            'account_id' => $this->account->id,
            'email' => $this->email,
            'role' => $this->role,
            'token' => Str::random(64),
            'invited_by' => $this->user->id,
            'expires_at' => now()->addDays(7),
        ]);
    }

    private function sendEmail(): void
    {
        Mail::to($this->email)->queue(new AccountInvitation($this->invitation));
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::MemberInvited,
            parameters: ['email' => $this->email],
        )->onQueue('low');
    }
}
