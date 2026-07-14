<?php

declare(strict_types=1);
use App\Actions\DestroyLocation;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a location', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $location = Location::factory()->create(['account_id' => $account->id]);

    new DestroyLocation(
        user: $owner,
        location: $location,
    )->execute();

    $this->assertDatabaseMissing('locations', ['id' => $location->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::LocationDeletion,
    );
});

it('cascades the delete to child locations', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $shelf = Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf A']);
    $box = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box 1', 'parent_id' => $shelf->id]);

    new DestroyLocation(
        user: $owner,
        location: $shelf,
    )->execute();

    $this->assertDatabaseMissing('locations', ['id' => $box->id]);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $location = Location::factory()->create(['account_id' => $account->id]);

    new DestroyLocation(
        user: $viewer,
        location: $location,
    )->execute();
});
