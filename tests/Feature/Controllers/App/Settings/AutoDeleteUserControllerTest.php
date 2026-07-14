<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('enables auto delete user', function () {
    $user = $this->createUser([
        'auto_delete_user' => false,
    ]);

    $response = $this->actingAs($user)
        ->from('/settings/security')
        ->put('/settings/security/auto-delete-account', [
            'auto_delete_user' => 'yes',
        ]);

    $response->assertRedirect('/settings/security');
});
