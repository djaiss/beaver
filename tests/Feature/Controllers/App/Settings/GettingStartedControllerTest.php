<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('brings the getting started screen back from the account settings', function () {
    Queue::fake();

    $account = $this->createAccount();
    $account->update(['show_getting_started' => false]);
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $response = $this->actingAs($owner)->put('/settings/getting-started', [
        'show_getting_started' => 'yes',
    ]);

    $response->assertRedirect(route('settings.index', absolute: false))
        ->assertSessionHas('status', 'Account updated successfully');

    expect($account->refresh()->show_getting_started)->toBeTrue();
});

it('hides the getting started screen from the account settings', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $this->actingAs($owner)->put('/settings/getting-started', ['show_getting_started' => 'no']);

    expect($account->refresh()->show_getting_started)->toBeFalse();
});

it('rejects a value that is neither yes nor no', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $this->actingAs($owner)->put('/settings/getting-started', ['show_getting_started' => 'maybe'])
        ->assertSessionHasErrors('show_getting_started');

    expect($account->refresh()->show_getting_started)->toBeTrue();
});

it('requires the value', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $this->actingAs($owner)->put('/settings/getting-started', [])
        ->assertSessionHasErrors('show_getting_started');
});

it('shows the toggle on the account settings screen', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $this->actingAs($owner)->get('/settings')
        ->assertOk()
        ->assertSee('Getting started screen')
        ->assertSee('Show the getting started screen');
});

it('does not let an editor change the setting', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $this->actingAs($editor)->put('/settings/getting-started', ['show_getting_started' => 'no'])
        ->assertForbidden();

    expect($account->refresh()->show_getting_started)->toBeTrue();
});

it('does not let a viewer change the setting', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->actingAs($viewer)->put('/settings/getting-started', ['show_getting_started' => 'no'])
        ->assertForbidden();

    expect($account->refresh()->show_getting_started)->toBeTrue();
});
