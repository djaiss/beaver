<?php

declare(strict_types=1);

use App\Actions\DestroyUserAsInstanceAdministrator;
use App\Enums\PermissionEnum;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('deletes any user on the instance', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Owner->value,
    ]);
    $ross = $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Editor->value,
    ]);

    new DestroyUserAsInstanceAdministrator(
        user: $monica,
        userToDelete: $ross,
    )->execute();

    $this->assertModelMissing($ross);
});

it('forbids a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ross = $this->createUser();

    $this->expectException(ModelNotFoundException::class);

    new DestroyUserAsInstanceAdministrator(
        user: $rachel,
        userToDelete: $ross,
    )->execute();
});

it('forbids an administrator from deleting themselves', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->expectException(ModelNotFoundException::class);

    new DestroyUserAsInstanceAdministrator(
        user: $monica,
        userToDelete: $monica,
    )->execute();
});

it('refuses to delete the last owner of an account', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $onlyOwner = $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Owner->value,
    ]);
    $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Viewer->value,
    ]);

    $this->expectException(ValidationException::class);

    new DestroyUserAsInstanceAdministrator(
        user: $monica,
        userToDelete: $onlyOwner,
    )->execute();
});

it('deletes an owner when the account still has another one', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');
    $firstOwner = $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Owner->value,
    ]);
    $this->createUser([
        'account_id' => $centralPerk->id,
        'role' => PermissionEnum::Owner->value,
    ]);

    new DestroyUserAsInstanceAdministrator(
        user: $monica,
        userToDelete: $firstOwner,
    )->execute();

    $this->assertModelMissing($firstOwner);
});
