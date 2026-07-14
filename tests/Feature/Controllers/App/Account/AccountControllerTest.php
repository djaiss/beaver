<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('shows the account settings page for an owner', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('settings');

    $response->assertOk();
    $response->assertViewIs('app.settings.account.index');
    $response->assertViewHas('account');
});
it('forbids a non owner from viewing the account settings', function () {
    $user = $this->createUser(['role' => PermissionEnum::Viewer->value]);

    $response = $this->actingAs($user)->get('settings');

    $response->assertForbidden();
});
it('renames the account for an owner', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->put('settings', [
        'name' => 'Central Perk',
    ]);

    $response->assertRedirect(route('settings.index', absolute: false));
    expect($user->account->fresh()->name)->toBe('Central Perk');
});
it('forbids a non owner from renaming the account', function () {
    $user = $this->createUser(['role' => PermissionEnum::Viewer->value]);

    $response = $this->actingAs($user)->put('settings', [
        'name' => 'Central Perk',
    ]);

    $response->assertForbidden();
});
it('deletes the account for an owner', function () {
    Queue::fake();

    $user = $this->createUser();
    $accountId = $user->account_id;

    $response = $this->actingAs($user)->delete('settings');

    $response->assertRedirect(route('register', absolute: false));
    $this->assertDatabaseMissing('accounts', ['id' => $accountId]);
});
it('forbids a non owner from deleting the account', function () {
    $user = $this->createUser(['role' => PermissionEnum::Viewer->value]);
    $accountId = $user->account_id;

    $response = $this->actingAs($user)->delete('settings');

    $response->assertForbidden();
    $this->assertDatabaseHas('accounts', ['id' => $accountId]);
});
