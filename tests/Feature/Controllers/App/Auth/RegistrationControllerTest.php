<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('shows the create account page', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

it('creates a user with their own account', function () {
    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
        'terms' => '1',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.index', absolute: false));

    $user = User::query()->where('email', 'chandler.bing@friends.com')->firstOrFail();

    // A single account was created, owned by the new user.
    $account = $user->account;
    expect($user->role)->toBe(PermissionEnum::Owner->value);
    expect($account->name)->toBe('Chandler Bing');
    expect($account->created_by_id)->toBe($user->id);
    expect(Account::query()->count())->toBe(1);
});

it('refuses to sign anybody up who has not agreed to the terms', function () {
    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
    ]);

    $response->assertSessionHasErrors('terms');

    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

it('does not create a user without an anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake();

    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
        'terms' => '1',
    ]);

    $response->assertSessionHasErrors('cf-turnstile-response');
    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

it('creates a user when cloudflare accepts the anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
        'terms' => '1',
        'cf-turnstile-response' => 'token-from-the-widget',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.index', absolute: false));
});

it('does not create a user when cloudflare rejects the anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

    $response = $this->post('/register', [
        'first_name' => 'Chandler',
        'last_name' => 'Bing',
        'email' => 'chandler.bing@friends.com',
        'password' => '5UTHSmdj',
        'password_confirmation' => '5UTHSmdj',
        'terms' => '1',
        'cf-turnstile-response' => 'token-that-was-already-used',
    ]);

    $response->assertSessionHasErrors('cf-turnstile-response');
    $this->assertGuest();
    expect(User::query()->count())->toBe(0);
});

it('links to the terms of use and the privacy policy on the sign up page', function () {
    $this->get('/register')
        ->assertOk()
        ->assertSee(route('marketing.terms.index'))
        ->assertSee(route('marketing.privacy.index'));
});
