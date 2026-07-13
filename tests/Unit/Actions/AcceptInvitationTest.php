<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\AcceptInvitation;
use App\Enums\PermissionEnum;
use App\Models\AccountMember;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AcceptInvitationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_accepts_a_pending_invitation(): void
    {
        Queue::fake();

        $account = $this->createAccount();
        $user = $this->createUser(['email' => 'ross.geller@friends.com']);
        $invitation = Invitation::factory()->create([
            'account_id' => $account->id,
            'email' => 'ross.geller@friends.com',
            'role' => PermissionEnum::Editor->value,
        ]);

        $member = new AcceptInvitation(
            invitation: $invitation,
            user: $user,
        )->execute();

        $this->assertInstanceOf(AccountMember::class, $member);
        $this->assertDatabaseHas('account_user', [
            'account_id' => $account->id,
            'user_id' => $user->id,
            'role' => PermissionEnum::Editor->value,
        ]);
        $this->assertNotNull($invitation->fresh()->accepted_at);
    }

    #[Test]
    public function it_throws_when_the_invitation_is_not_pending(): void
    {
        Queue::fake();
        $this->expectException(ValidationException::class);

        $user = $this->createUser(['email' => 'ross.geller@friends.com']);
        $invitation = Invitation::factory()->expired()->create([
            'email' => 'ross.geller@friends.com',
        ]);

        new AcceptInvitation(
            invitation: $invitation,
            user: $user,
        )->execute();
    }

    #[Test]
    public function it_throws_when_the_email_does_not_match(): void
    {
        Queue::fake();
        $this->expectException(ValidationException::class);

        $user = $this->createUser(['email' => 'ross.geller@friends.com']);
        $invitation = Invitation::factory()->create([
            'email' => 'rachel.green@friends.com',
        ]);

        new AcceptInvitation(
            invitation: $invitation,
            user: $user,
        )->execute();
    }

    #[Test]
    public function it_throws_when_the_user_is_already_a_member(): void
    {
        Queue::fake();
        $this->expectException(ValidationException::class);

        $account = $this->createAccount();
        $user = $this->createUser(['email' => 'ross.geller@friends.com']);
        $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

        $invitation = Invitation::factory()->create([
            'account_id' => $account->id,
            'email' => 'ross.geller@friends.com',
        ]);

        new AcceptInvitation(
            invitation: $invitation,
            user: $user,
        )->execute();
    }
}
