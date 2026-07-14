<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('displays the security page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->get('/profile/security');

    $response->assertStatus(200);
    $response->assertViewIs('app.settings.security.index');
    $response->assertViewHas('apiKeys');
    $response->assertViewHas('has2fa');
});
