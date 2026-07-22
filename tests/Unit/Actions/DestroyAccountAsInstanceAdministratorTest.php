<?php

declare(strict_types=1);

use App\Actions\DestroyAccountAsInstanceAdministrator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('deletes any account on the instance', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $centralPerk = $this->createAccount('Central Perk');

    new DestroyAccountAsInstanceAdministrator(
        user: $monica,
        account: $centralPerk,
    )->execute();

    $this->assertModelMissing($centralPerk);
});

it('forbids a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $centralPerk = $this->createAccount('Central Perk');

    $this->expectException(ModelNotFoundException::class);

    new DestroyAccountAsInstanceAdministrator(
        user: $rachel,
        account: $centralPerk,
    )->execute();
});

it('forbids an administrator from deleting their own account', function () {
    $centralPerk = $this->createAccount('Central Perk');
    $monica = $this->createUser([
        'account_id' => $centralPerk->id,
        'is_instance_administrator' => true,
    ]);

    $this->expectException(ModelNotFoundException::class);

    new DestroyAccountAsInstanceAdministrator(
        user: $monica,
        account: $centralPerk,
    )->execute();
});
