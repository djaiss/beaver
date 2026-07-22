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
    $response->assertViewHas('currencies');
});

it('renders the field help popovers on the settings page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('settings');

    $response->assertOk();
    $response->assertSee('identifies the account to everyone you share it with');
    $response->assertSee('valuation totals across all of your collections');
    $response->assertSee('account-settings');
});

it('renders the section title help popovers on the settings page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('settings');

    $response->assertOk();
    $response->assertSee('welcome checklist that greets a new account');
    $response->assertSee('most destructive action in KolleK');
});

it('forbids a non owner from viewing the account settings', function () {
    $user = $this->createUser(['role' => PermissionEnum::Viewer->value]);

    $response = $this->actingAs($user)->get('settings');

    $response->assertForbidden();
});

it('forbids an editor from viewing the account settings', function () {
    $user = $this->createUser(['role' => PermissionEnum::Editor->value]);

    $response = $this->actingAs($user)->get('settings');

    $response->assertForbidden();
});

it('renames the account and sets its currency for an owner', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)->put('settings', [
        'name' => 'Central Perk',
        'currency_code' => 'EUR',
    ]);

    $response->assertRedirect(route('settings.index', absolute: false));
    expect($user->account->fresh()->name)->toBe('Central Perk');
    expect($user->account->fresh()->currency_code)->toBe('EUR');
});

it('rejects an unknown currency', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->put('settings', [
        'name' => 'Central Perk',
        'currency_code' => 'XYZ',
    ]);

    $response->assertSessionHasErrors('currency_code');
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
