<?php

declare(strict_types=1);
use App\Jobs\CheckLastLogin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PragmaRX\Google2FALaravel\Google2FA;

uses(RefreshDatabase::class);

it('displays the 2fa challenge page', function () {
    $user = $this->createUser();

    $response = $this->withSession(['2fa:user:id' => $user->id])
        ->get('/2fa-challenge');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.2fa');
    $response->assertViewHas('quote');
});

it('authenticates user with valid totp code', function () {
    Queue::fake();

    $google2fa = new Google2FA(request());
    $secret = $google2fa->generateSecretKey();

    $user = $this->createUser([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => now(),
    ]);

    $validCode = $google2fa->getCurrentOtp($secret);

    $response = $this->withSession(['2fa:user:id' => $user->id])
        ->post('/2fa-challenge', [
            'code' => $validCode,
        ]);

    $this->assertAuthenticated();
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard.index', absolute: false));

    expect(session()->has('2fa:user:id'))->toBeFalse();

    Queue::assertPushedOn(
        queue: 'low',
        job: CheckLastLogin::class,
        callback: fn (CheckLastLogin $job): bool => $job->user->id === $user->id,
    );
});

it('authenticates user with valid recovery code', function () {
    Queue::fake();

    $google2fa = new Google2FA(request());
    $secret = $google2fa->generateSecretKey();

    $user = $this->createUser([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => ['ABC123', 'DEF456', 'GHI789'],
    ]);

    $response = $this->withSession(['2fa:user:id' => $user->id])
        ->post('/2fa-challenge', [
            'code' => 'ABC123',
        ]);

    $this->assertAuthenticated();
    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('dashboard.index', absolute: false));

    $user->refresh();
    expect($user->two_factor_recovery_codes)->not->toContain('ABC123');
    expect($user->two_factor_recovery_codes)->toHaveCount(2);

    Queue::assertPushedOn(
        queue: 'low',
        job: CheckLastLogin::class,
    );
});

it('rejects invalid code', function () {
    $google2fa = new Google2FA(request());
    $secret = $google2fa->generateSecretKey();

    $user = $this->createUser([
        'two_factor_secret' => $secret,
        'two_factor_confirmed_at' => now(),
    ]);

    $response = $this->withSession(['2fa:user:id' => $user->id])
        ->post('/2fa-challenge', [
            'code' => 'invalid-code',
        ]);

    $this->assertGuest();
    $response->assertRedirect();
    $response->assertSessionHasErrors(['code' => 'Invalid code']);
});
