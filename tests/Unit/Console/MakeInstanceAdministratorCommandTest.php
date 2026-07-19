<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('grants the instance administration', function () {
    $monica = $this->createUser([
        'email' => 'monica@friends.com',
        'is_instance_administrator' => false,
    ]);

    $this->artisan('beaver:make-instance-administrator', ['email' => 'monica@friends.com'])
        ->assertSuccessful();

    expect($monica->refresh()->isInstanceAdministrator())->toBeTrue();
});

it('revokes the instance administration', function () {
    $monica = $this->createUser([
        'email' => 'monica@friends.com',
        'is_instance_administrator' => true,
    ]);

    $this->artisan('beaver:make-instance-administrator', [
        'email' => 'monica@friends.com',
        '--revoke' => true,
    ])->assertSuccessful();

    expect($monica->refresh()->isInstanceAdministrator())->toBeFalse();
});

it('fails when no user has this email', function () {
    $this->artisan('beaver:make-instance-administrator', ['email' => 'gunther@friends.com'])
        ->assertFailed();
});
