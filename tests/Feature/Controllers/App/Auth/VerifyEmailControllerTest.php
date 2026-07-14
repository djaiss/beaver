<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('verifies the email with valid link', function () {
    Event::fake();

    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    expect($user->fresh()->email_verified_at)->not->toBeNull();
    Event::assertDispatched(Verified::class);
    $response->assertRedirect(route('dashboard.index', absolute: false).'?verified=1');
});

it('redirects to dashboard if email is already verified', function () {
    Event::fake();

    $user = $this->createUser();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this->actingAs($user)->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    $response->assertRedirect(route('dashboard.index', absolute: false).'?verified=1');
});

it('rejects invalid verification links', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get("/verify-email/{$user->id}/invalid-hash");

    $response->assertStatus(403);
    expect($user->fresh()->email_verified_at)->toBeNull();
});
