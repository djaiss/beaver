<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the recovery codes', function () {
    $user = $this->createUser([
        'two_factor_recovery_codes' => ['code1', 'code2', 'code3'],
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/security/recovery-codes');

    $response->assertOk();
});
