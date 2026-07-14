<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows the user to update their password', function () {
    $user = $this->createUser([
        'password' => bcrypt('5UTHSmdj'),
    ]);

    $response = $this->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/security/password', [
            'current_password' => '5UTHSmdj',
            'new_password' => 'new-5UTHSmdj',
            'new_password_confirmation' => 'new-5UTHSmdj',
        ]);

    $response->assertRedirect('/settings/security');
});
