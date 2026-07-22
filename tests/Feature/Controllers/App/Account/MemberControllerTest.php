<?php

declare(strict_types=1);
use App\Actions\UpdateUserAvatar;
use App\Enums\PermissionEnum;
use App\Mail\AccountInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('lists the members for an owner', function () {
    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get('settings/members');

    $response->assertOk();
    $response->assertViewIs('app.settings.members.index');
});

it('renders the section title help popovers on the members page', function () {
    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get('settings/members');

    $response->assertOk();
    $response->assertSee('role that decides what they can do here');
    $response->assertSee('Brings someone new into this account');
});

it('forbids a non owner from listing the members', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->get('settings/members');

    $response->assertForbidden();
});

it('forbids an editor from listing the members', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Editor->value);

    $response = $this->actingAs($user)->get('settings/members');

    $response->assertForbidden();
});

it('sends an invitation', function () {
    Queue::fake();
    Mail::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->post('settings/members', [
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertRedirect(route('settings.members.index', absolute: false));
    $this->assertDatabaseHas('invitations', [
        'account_id' => $account->id,
        'email' => 'phoebe.buffay@friends.com',
        'role' => PermissionEnum::Editor->value,
    ]);
    Mail::assertQueued(AccountInvitation::class);
});

it('previews the invitation email without an email address', function () {
    $owner = $this->createUser();
    $account = $this->createAccount(name: 'Central Perk');
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get('settings/members?preview=1&role=editor');

    $response->assertOk();
    $response->assertViewIs('app.settings.members.index');
    $response->assertViewHas('showPreview', true);
    $response->assertSee('Central Perk', escape: false);
    $response->assertSee('This is a preview. Links are disabled and nothing has been sent.');
    $this->assertDatabaseCount('invitations', 0);
});

it('does not show a preview on a normal page load', function () {
    $owner = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->get('settings/members');

    $response->assertOk();
    $response->assertViewHas('showPreview', false);
});

it('forbids a non owner from previewing the invitation email', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->get('settings/members?preview=1&role=editor');

    $response->assertForbidden();
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

    $response = $this->actingAs($owner)->put("settings/members/{$member->id}", [
        'role' => PermissionEnum::Editor->value,
    ]);

    $response->assertRedirect(route('settings.members.index', absolute: false));
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

    $response = $this->actingAs($owner)->delete("settings/members/{$member->id}");

    $response->assertRedirect(route('settings.members.index', absolute: false));
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

    $response = $this->actingAs($owner)->delete("settings/members/{$foreignMember->id}");

    $response->assertNotFound();
});

it('cannot remove the last owner', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->delete("settings/members/{$member->id}");

    $response->assertSessionHasErrors('member');
    $this->assertDatabaseHas('users', ['id' => $member->id]);
});

it('cannot demote the last owner', function () {
    Queue::fake();

    $owner = $this->createUser();
    $account = $this->createAccount();
    $member = $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->put("settings/members/{$member->id}", [
        'role' => PermissionEnum::Viewer->value,
    ]);

    $response->assertSessionHasErrors('role');
    expect($member->fresh()->role)->toBe(PermissionEnum::Owner->value);
});

it('shows the avatar of a member when they have one, and the initials otherwise', function () {
    Storage::fake();

    $owner = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $owner->update(['role' => PermissionEnum::Owner->value]);

    $rachel = $this->createUser(['first_name' => 'Rachel', 'last_name' => 'Green']);
    $rachel->update(['account_id' => $owner->account_id]);

    new UpdateUserAvatar(
        user: $rachel,
        file: UploadedFile::fake()->image('rachel.jpg', 400, 400),
    )->execute();

    $response = $this->actingAs($owner)->get(route('settings.members.index'));

    $response->assertOk();
    $response->assertSee(route('profile.avatar.show', ['user' => $rachel, 'size' => 32]), escape: false);

    // Monica never uploaded one, so she keeps her initials.
    $response->assertSee('MG');
});
