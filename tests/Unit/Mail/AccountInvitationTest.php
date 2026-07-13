<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\AccountInvitation;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AccountInvitationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_the_correct_envelope_subject(): void
    {
        $account = $this->createAccount(name: 'Central Perk');
        $invitation = Invitation::factory()->create(['account_id' => $account->id]);

        $mailable = new AccountInvitation(invitation: $invitation);

        $this->assertStringContainsString('invited', $mailable->envelope()->subject);
    }

    #[Test]
    public function it_renders_the_account_name_and_the_accept_link(): void
    {
        $account = $this->createAccount(name: 'Central Perk');
        $invitation = Invitation::factory()->create(['account_id' => $account->id]);

        $mailable = new AccountInvitation(invitation: $invitation);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Central Perk', $rendered);
        $this->assertStringContainsString(route('invitations.show', $invitation->token), $rendered);
    }
}
