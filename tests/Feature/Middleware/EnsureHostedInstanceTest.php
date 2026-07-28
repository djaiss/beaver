<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->owner = $this->createUser();
    $this->assignUserToAccount(user: $this->owner, account: $this->createAccount(), role: PermissionEnum::Owner->value);
});

it('lets a request through on the hosted instance', function () {
    config(['pricing.hosted' => true]);

    $this->actingAs($this->owner)->get(route('upgrade.index'))->assertOk();
});

it('answers 404 rather than 403 on a self hosted instance', function () {
    config(['pricing.hosted' => false]);

    // 404 and not 403: a self hosted instance should not even announce that
    // these screens exist, the way the support section does when it is off.
    $this->actingAs($this->owner)->get(route('upgrade.index'))->assertNotFound();
    $this->actingAs($this->owner)->get(route('upgrade.confirm.new'))->assertNotFound();
    $this->actingAs($this->owner)->post(route('upgrade.confirm.create'))->assertNotFound();
});
