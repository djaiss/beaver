<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FALaravel\Google2FA;

uses(RefreshDatabase::class);

it('displays the 2fa setup page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->get('/profile/security/2fa/new');

    $response->assertStatus(200);
    $response->assertViewIs('app.settings.security._2fa-new');
    $response->assertViewHas('secret');
    $response->assertViewHas('qrCodeSvg');
    $user->refresh();
    expect($user->two_factor_secret)->not->toBeNull();
});
it('enables 2fa with valid token', function () {
    $google2fa = new Google2FA(request());
    $secret = $google2fa->generateSecretKey();

    $user = $this->createUser([
        'two_factor_secret' => $secret,
    ]);

    $validToken = $google2fa->getCurrentOtp($secret);

    $response = $this->actingAs($user)
        ->from('/profile/security/2fa/new')
        ->post('/profile/security/2fa', [
            'token' => $validToken,
        ]);

    $response->assertRedirect('/profile/security');
    $response->assertSessionHas('status', 'Two-factor authentication has been enabled successfully.');

    $user->refresh();
    expect($user->two_factor_confirmed_at)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->not->toBeNull();
    expect($user->two_factor_recovery_codes)->toHaveCount(8);
});
it('rejects invalid token', function () {
    $google2fa = new Google2FA(request());
    $secret = $google2fa->generateSecretKey();

    $user = $this->createUser([
        'two_factor_secret' => $secret,
    ]);

    $response = $this->actingAs($user)
        ->from('/profile/security/2fa/new')
        ->post('/profile/security/2fa', [
            'token' => '000000',
        ]);

    $response->assertRedirect('/profile/security/2fa/new');
    $response->assertSessionHasErrors(['token' => 'The provided token is invalid.']);

    $user->refresh();
    expect($user->two_factor_confirmed_at)->toBeNull();
});
it('removes 2fa from user account', function () {
    $user = $this->createUser([
        'two_factor_secret' => 'test-secret',
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => ['code1', 'code2'],
    ]);

    $response = $this->actingAs($user)
        ->from('/profile/security')
        ->delete('/profile/security/2fa');

    $response->assertRedirect('/profile/security');
});
