<?php

declare(strict_types=1);
use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('has many emails sent', function () {
    $user = $this->createUser();
    EmailSent::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($user->emailsSent()->exists())->toBeTrue();
});

it('gets the initials', function () {
    $ross = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
    ]);

    expect($ross->initials())->toEqual('RG');
});

it('gets the full name', function () {
    $user = User::factory()->create([
        'first_name' => 'Ross',
        'last_name' => 'Geller',
    ]);

    expect($user->getFullName())->toEqual('Ross Geller');
});

it('encrypts the two factor secret and recovery codes at rest', function () {
    $secret = 'JBSWY3DPEHPK3PXP';
    $recoveryCodes = ['ABC123', 'DEF456'];

    $user = User::factory()->create([
        'two_factor_secret' => $secret,
        'two_factor_recovery_codes' => $recoveryCodes,
    ]);

    // The values are decrypted transparently when read through the model.
    expect($user->fresh()->two_factor_secret)->toBe($secret);
    expect($user->fresh()->two_factor_recovery_codes)->toBe($recoveryCodes);

    // But the raw database values are encrypted, not plaintext.
    $raw = DB::table('users')->where('id', $user->id)->first();
    $this->assertNotSame($secret, $raw->two_factor_secret);
    $this->assertStringNotContainsString($secret, (string) $raw->two_factor_secret);
    $this->assertStringNotContainsString('ABC123', (string) $raw->two_factor_recovery_codes);
});

it('knows whether it administers the instance', function () {
    $monica = User::factory()->instanceAdministrator()->create();
    $ross = User::factory()->create();

    expect($monica->isInstanceAdministrator())->toBeTrue();
    expect($ross->isInstanceAdministrator())->toBeFalse();
});
