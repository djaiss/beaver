<?php

declare(strict_types=1);
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

uses(RefreshDatabase::class);

it('invites a person to an account', function () {
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

    expect($invitation)->toBeInstanceOf(Invitation::class);
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
});
it('throws when the user is not an owner', function () {
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
});
it('throws when the role is invalid', function () {
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
});
it('throws when the email is already a member', function () {
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
});
it('throws when a pending invitation already exists', function () {
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
});
