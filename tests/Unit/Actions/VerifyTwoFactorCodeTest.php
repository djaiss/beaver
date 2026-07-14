<?php

declare(strict_types=1);
use App\Actions\VerifyTwoFactorCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('verifies a rescue code and updates last activity', function () {
    $user = User::factory()->create([
        'two_factor_recovery_codes' => ['code-one', 'code-two'],
    ]);

    $result = new VerifyTwoFactorCode(
        user: $user,
        code: 'code-one',
    )->execute();

    expect($result)->toBeTrue();
    expect($user->refresh()->two_factor_recovery_codes)->not->toContain('code-one');
});
it('returns false and skips activity update for invalid codes', function () {
    $user = User::factory()->create([
        'two_factor_recovery_codes' => ['code-one'],
    ]);

    $result = new VerifyTwoFactorCode(
        user: $user,
        code: 'wrong-code',
    )->execute();

    expect($result)->toBeFalse();
    expect($user->refresh()->two_factor_recovery_codes)->toEqual(['code-one']);
});
