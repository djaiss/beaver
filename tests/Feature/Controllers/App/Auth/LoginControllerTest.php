<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Jobs\SendEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('renders the login screen', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});

it('authenticates a user', function () {
    config(['app.show_marketing_site' => false]);
    $user = $this->createUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.index', absolute: false));
});

it('sends an email on failed login', function () {
    Queue::fake();
    config(['app.show_marketing_site' => false]);

    $user = $this->createUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => $job->emailType === EmailType::LoginFailed && $job->user->id === $user->id,
    );
});

it('does not authenticate a user with invalid password', function () {
    config(['app.show_marketing_site' => false]);
    $user = $this->createUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

it('does not show the anti-spam widget when it is turned off', function () {
    $this->get('/login')
        ->assertOk()
        ->assertDontSee('cf-turnstile');
});

it('shows the anti-spam widget when it is turned on', function () {
    config(['turnstile.enabled' => true]);
    config(['turnstile.site_key' => 'site-key-of-the-one-with-the-widget']);

    $this->get('/login')
        ->assertOk()
        ->assertSee('cf-turnstile')
        ->assertSee('site-key-of-the-one-with-the-widget');
});

it('does not sign anybody in without an anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake();

    $user = $this->createUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors('cf-turnstile-response');
    $this->assertGuest();
    Http::assertNothingSent();
});

it('signs a user in when cloudflare accepts the anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

    $user = $this->createUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'cf-turnstile-response' => 'token-from-the-widget',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard.index', absolute: false));
});

it('does not sign anybody in when cloudflare rejects the anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => false])]);

    $user = $this->createUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
        'cf-turnstile-response' => 'token-that-was-already-used',
    ]);

    $response->assertSessionHasErrors('cf-turnstile-response');
    $this->assertGuest();
});

it('logs out a user', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
