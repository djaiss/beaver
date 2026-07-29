<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('renders the forgot password screen', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.forgot-password');
});

it('sends a password reset link', function () {
    Notification::fake();

    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'chandler.bing@friends.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
});

it('does not send a password reset link without an anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Notification::fake();
    Http::fake();

    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'chandler.bing@friends.com',
    ]);

    $response->assertSessionHasErrors('cf-turnstile-response');
    Notification::assertNothingSent();
});

it('sends a password reset link when cloudflare accepts the anti-spam token', function () {
    config(['turnstile.enabled' => true]);
    Notification::fake();
    Http::fake(['challenges.cloudflare.com/*' => Http::response(['success' => true])]);

    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'chandler.bing@friends.com',
        'cf-turnstile-response' => 'token-from-the-widget',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
});
