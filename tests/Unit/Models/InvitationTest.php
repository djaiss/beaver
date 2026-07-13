<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\Account;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_an_account(): void
    {
        $account = $this->createAccount();
        $invitation = Invitation::factory()->create(['account_id' => $account->id]);

        $this->assertTrue($invitation->account()->exists());
        $this->assertInstanceOf(Account::class, $invitation->account);
    }

    #[Test]
    public function it_belongs_to_the_user_who_sent_it(): void
    {
        $inviter = $this->createUser();
        $invitation = Invitation::factory()->create(['invited_by' => $inviter->id]);

        $this->assertTrue($invitation->invitedBy()->exists());
        $this->assertInstanceOf(User::class, $invitation->invitedBy);
        $this->assertSame($inviter->id, $invitation->invitedBy->id);
    }

    #[Test]
    public function it_knows_when_it_is_expired(): void
    {
        $pending = Invitation::factory()->create();
        $expired = Invitation::factory()->expired()->create();

        $this->assertFalse($pending->isExpired());
        $this->assertTrue($expired->isExpired());
    }

    #[Test]
    public function it_is_pending_when_not_accepted_and_not_expired(): void
    {
        $invitation = Invitation::factory()->create();

        $this->assertTrue($invitation->isPending());
    }

    #[Test]
    public function it_is_not_pending_when_expired(): void
    {
        $invitation = Invitation::factory()->expired()->create();

        $this->assertFalse($invitation->isPending());
    }

    #[Test]
    public function it_is_not_pending_when_accepted(): void
    {
        $invitation = Invitation::factory()->accepted()->create();

        $this->assertFalse($invitation->isPending());
    }
}
