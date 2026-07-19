<?php

declare(strict_types=1);

use App\Actions\ToggleInstanceAdministrator;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('grants the instance administration to another user', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ross = $this->createUser(['is_instance_administrator' => false]);

    $user = new ToggleInstanceAdministrator(
        user: $monica,
        userToToggle: $ross,
        isInstanceAdministrator: true,
    )->execute();

    expect($user)->toBeInstanceOf(User::class);
    $this->assertDatabaseHas('users', [
        'id' => $ross->id,
        'is_instance_administrator' => true,
    ]);
});

it('revokes the instance administration from another user', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ross = $this->createUser(['is_instance_administrator' => true]);

    new ToggleInstanceAdministrator(
        user: $monica,
        userToToggle: $ross,
        isInstanceAdministrator: false,
    )->execute();

    $this->assertDatabaseHas('users', [
        'id' => $ross->id,
        'is_instance_administrator' => false,
    ]);
});

it('forbids a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ross = $this->createUser(['is_instance_administrator' => false]);

    $this->expectException(ModelNotFoundException::class);

    new ToggleInstanceAdministrator(
        user: $rachel,
        userToToggle: $ross,
        isInstanceAdministrator: true,
    )->execute();
});

it('forbids an administrator from revoking their own access', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->expectException(ModelNotFoundException::class);

    new ToggleInstanceAdministrator(
        user: $monica,
        userToToggle: $monica,
        isInstanceAdministrator: false,
    )->execute();
});
