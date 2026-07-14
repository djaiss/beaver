<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Mail\AccountInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the members for an owner', function () {
    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get("accounts/{$account->id}/members");

    $response->assertOk();
    $response->assertViewIs('app.account.members.index');
});
it('forbids a non owner from listing the members', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->get("accounts/{$account->id}/members");

    $response->assertForbidden();
});
it('sends an invitation', function () {
    Queue::fake();
    Mail::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->post("accounts/{$account->id}/members", [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
    $this->assertDatabaseHas('invitations', [
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
    ]);
    Mail::assertQueued(AccountInvitation::class);
});
it('updates the role of a member', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    $response = $this->actingAs($owner)->put("accounts/{$account->id}/members/{$member->id}", [
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
    expect($member->fresh()->role)->toBe(PermissionEnum::Editor->value);
});
it('removes a member', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $member = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $account,
        role: PermissionEnum::Viewer->value,
    );

    $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$member->id}");

    $response->assertRedirect(route('accounts.members.index', $account->id, absolute: false));
    $this->assertModelMissing($member);
});
it('returns not found for a member of another account', function () {
    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $otherAccount = $this->createAccount();
    $foreignMember = $this->assignUserToAccount(
        user: $this->createUser(),
        account: $otherAccount,
        role: PermissionEnum::Viewer->value,
    );

    $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$foreignMember->id}");

    $response->assertNotFound();
});
it('cannot remove the last owner', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->delete("accounts/{$account->id}/members/{$member->id}");

    $response->assertSessionHasErrors('member');
    $this->assertDatabaseHas('account_user', ['id' => $member->id]);
});
it('cannot demote the last owner', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->put("accounts/{$account->id}/members/{$member->id}", [
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response->assertSessionHasErrors('role');
    expect($member->fresh()->role)->toBe(PermissionEnum::Owner->value);
});
