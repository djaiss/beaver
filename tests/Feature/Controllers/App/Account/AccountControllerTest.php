<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the accounts of the user', function () {
    $user = $this->createUser();
    $account = $this->createAccount(name: 'Central Perk');
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($user)->get('accounts');

    $response->assertOk();
    $response->assertViewIs('app.account.index');
    $response->assertViewHas('accounts');
});
it('shows the create account page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('accounts/new');

    $response->assertOk();
});
it('creates an account with an owner membership', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->post('accounts', [
        'name' => 'Central Perk',
    ]);

    $account = $user->accounts()->firstOrFail();

    $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
    expect($account->name)->toBe('Central Perk');
    $this->assertDatabaseHas('account_user', [
        'account_id' => $account->id,
        'user_id' => $user->id,
        'role' => PermissionEnum::Owner->value,
    ]);
});
it('shows an account to a member', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->get("accounts/{$account->id}");

    $response->assertOk();
    $response->assertViewIs('app.account.show');
});
it('forbids a non member from viewing an account', function () {
    $user = $this->createUser();
    $account = $this->createAccount();

    $response = $this->actingAs($user)->get("accounts/{$account->id}");

    $response->assertForbidden();
});
it('returns not found for a missing account', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('accounts/999999');

    $response->assertNotFound();
});
it('renames an account for an owner', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $this->createAccount(name: 'Old name');
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($user)->put("accounts/{$account->id}", [
        'name' => 'Central Perk',
    ]);

    $response->assertRedirect(route('accounts.show', $account->id, absolute: false));
    expect($account->fresh()->name)->toBe('Central Perk');
});
it('forbids a non owner from renaming an account', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->put("accounts/{$account->id}", [
        'name' => 'Central Perk',
    ]);

    $response->assertForbidden();
});
it('deletes an account for an owner', function () {
    Queue::fake();

    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($user)->delete("accounts/{$account->id}");

    $response->assertRedirect(route('accounts.index', absolute: false));
    $this->assertDatabaseMissing('accounts', ['id' => $account->id]);
});
it('forbids a non owner from deleting an account', function () {
    $user = $this->createUser();
    $account = $this->createAccount();
    $this->assignUserToAccount(user: $user, account: $account, role: PermissionEnum::Viewer->value);

    $response = $this->actingAs($user)->delete("accounts/{$account->id}");

    $response->assertForbidden();
    $this->assertDatabaseHas('accounts', ['id' => $account->id]);
});
