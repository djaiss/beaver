<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Mail\AccountInvitation;
use App\Models\Account;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

/**
 * Render the invitation email as a safe, inert fragment for an inline preview.
 * Nothing is persisted or sent: the invitation is built in memory, and every
 * link is stripped the same way CreateEmailSent strips links from a sent
 * email's stored body.
 */
class PreviewAccountInvitation
{
    public function __construct(
        private readonly Account $account,
        private string $role = PermissionEnum::Viewer->value,
    ) {}

    public function execute(): string
    {
        if (PermissionEnum::tryFrom($this->role) === null) {
            $this->role = PermissionEnum::Viewer->value;
        }

        $invitation = new Invitation([
            'account_id' => $this->account->id,
            'role' => $this->role,
            'token' => Str::random(64),
        ]);
        $invitation->setRelation('account', $this->account);

        $html = new AccountInvitation($invitation)->render();

        return Purify::config(['HTML.ForbiddenElements' => 'a'])->clean($html);
    }
}
