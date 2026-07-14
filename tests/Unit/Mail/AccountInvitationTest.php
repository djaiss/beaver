<?php

declare(strict_types=1);
use App\Mail\AccountInvitation;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('has the correct envelope subject', function () {
    $account = $this->createAccount(name: 'Central Perk');
    $invitation = Invitation::factory()->create(['account_id' => $account->id]);

    $mailable = new AccountInvitation(invitation: $invitation);

    $this->assertStringContainsString('invited', $mailable->envelope()->subject);
});

it('renders the account name and the accept link', function () {
    $account = $this->createAccount(name: 'Central Perk');
    $invitation = Invitation::factory()->create(['account_id' => $account->id]);

    $mailable = new AccountInvitation(invitation: $invitation);

    $rendered = $mailable->render();

    $this->assertStringContainsString('Central Perk', $rendered);
    $this->assertStringContainsString(route('invitations.show', $invitation->token), $rendered);
});
