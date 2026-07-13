<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\InviteToAccount;
use App\Enums\PermissionEnum;
use App\Jobs\LogUserAction;
use App\Mail\AccountInvitation;
use App\Models\Invitation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InviteToAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_invites_a_person_to_an_account(): void
    {
        Queue::fake();
        Mail::fake();

        $account = $this->createAccount();
        $owner = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $invitation = new InviteToAccount(
            user: $owner,
            account: $account,
            email: 'phoebe.buffay@friends.com',
            role: PermissionEnum::Editor->value,
        )->execute();

        $this->assertInstanceOf(Invitation::class, $invitation);
        $this->assertDatabaseHas('invitations', [
            'id' => $invitation->id,
            'account_id' => $account->id,
            'email' => 'phoebe.buffay@friends.com',
            'role' => PermissionEnum::Editor->value,
            'invited_by' => $owner->id,
        ]);

        Mail::assertQueued(
            AccountInvitation::class,
            fn (AccountInvitation $mail): bool => $mail->hasTo('phoebe.buffay@friends.com'),
        );

        Queue::assertPushedOn(queue: 'low', job: LogUserAction::class);
    }

    #[Test]
    public function it_throws_when_the_user_is_not_an_owner(): void
    {
        Mail::fake();
        $this->expectException(ModelNotFoundException::class);

        $account = $this->createAccount();
        $viewer = $this->createUser();
        $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

        new InviteToAccount(
            user: $viewer,
            account: $account,
            email: 'phoebe.buffay@friends.com',
        )->execute();
    }

    #[Test]
    public function it_throws_when_the_role_is_invalid(): void
    {
        Mail::fake();
        $this->expectException(ValidationException::class);

        $account = $this->createAccount();
        $owner = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        new InviteToAccount(
            user: $owner,
            account: $account,
            email: 'phoebe.buffay@friends.com',
            role: 'superhero',
        )->execute();
    }

    #[Test]
    public function it_throws_when_the_email_is_already_a_member(): void
    {
        Mail::fake();
        $this->expectException(ValidationException::class);

        $account = $this->createAccount();
        $owner = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        $existing = $this->createUser(['email' => 'phoebe.buffay@friends.com']);
        $this->assignUserToAccount(user: $existing, account: $account, role: PermissionEnum::Viewer->value);

        new InviteToAccount(
            user: $owner,
            account: $account,
            email: 'phoebe.buffay@friends.com',
        )->execute();
    }

    #[Test]
    public function it_throws_when_a_pending_invitation_already_exists(): void
    {
        Mail::fake();
        $this->expectException(ValidationException::class);

        $account = $this->createAccount();
        $owner = $this->createUser();
        $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

        Invitation::factory()->create([
            'account_id' => $account->id,
            'email' => 'phoebe.buffay@friends.com',
        ]);

        new InviteToAccount(
            user: $owner,
            account: $account,
            email: 'phoebe.buffay@friends.com',
        )->execute();
    }
}
