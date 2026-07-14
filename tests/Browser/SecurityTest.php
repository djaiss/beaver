<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('changes the password', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Change password')
        ->fill('current_password', 'password')
        ->fill('new_password', '5UTHSmdj')
        ->fill('new_password_confirmation', '5UTHSmdj')
        ->click('form[action$="/profile/security/password"] button[type="submit"]')
        ->assertPathIs('/profile/security')
        ->assertSee('Changes saved');

    expect(Hash::check('5UTHSmdj', $user->fresh()->password))->toBeTrue();
});

it('enables automatic account deletion', function () {
    $user = User::factory()->create(['auto_delete_user' => false]);
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Auto delete account')
        ->select('auto_delete_user', 'yes')
        ->click('#auto-delete-account-form button[type="submit"]')
        ->assertPathIs('/profile/security');

    expect($user->fresh()->auto_delete_user)->toBeTrue();
});

it('creates an api key', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Personal API Keys')
        ->click('@new-api-key-button')
        ->fill('label', 'Deploy key')
        ->click('@create-api-key-button')
        ->assertPathIs('/profile/security')
        ->assertSee('API Key created successfully');

    expect($user->fresh()->tokens()->where('name', 'Deploy key')->exists())->toBeTrue();
});

it('deletes an api key', function () {
    $user = User::factory()->create();
    $token = $user->createToken('Old key');
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Old key')
        ->click('@delete-api-key-'.$token->accessToken->id)
        ->assertPathIs('/profile/security')
        ->assertSee('API key deleted');

    expect($user->fresh()->tokens()->count())->toBe(0);
});

it('starts the two-factor authentication setup', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Two-factor authentication')
        ->click('Set up')
        ->assertSee('Setup key:');

    expect($user->fresh()->two_factor_secret)->not->toBeNull();
});

it('shows two-factor authentication as configured', function () {
    $user = User::factory()->create([
        'two_factor_secret' => 'ABCDEFGHIJKLMNOP',
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => ['code-one', 'code-two'],
    ]);
    $this->actingAs($user);

    visit('/profile/security')
        ->assertSee('Configured')
        ->assertSee('Recovery codes');
});
