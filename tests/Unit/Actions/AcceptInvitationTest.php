<?php

declare(strict_types=1);
use App\Actions\AcceptInvitation;
use App\Enums\PermissionEnum;
use App\Models\AccountMember;
use App\Models\Invitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('accepts a pending invitation', function () {
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

    expect($member)->toBeInstanceOf(AccountMember::class);
    $this->assertDatabaseHas('account_user', [
        'account_id' => $account->id,
        'user_id' => $user->id,
        'role' => PermissionEnum::Editor->value,
    ]);
    expect($invitation->fresh()->accepted_at)->not->toBeNull();
});
it('throws when the invitation is not pending', function () {
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
});
it('throws when the email does not match', function () {
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
});
it('throws when the user is already a member', function () {
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
});
