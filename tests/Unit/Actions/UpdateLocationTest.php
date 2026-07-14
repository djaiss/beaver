<?php

declare(strict_types=1);
use App\Actions\UpdateLocation;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates a location and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $location = Location::factory()->create(['account_id' => $account->id, 'name' => 'Old name']);

    $result = new UpdateLocation(
        user: $editor,
        location: $location,
        name: 'Shelf A',
    )->execute();

    expect($result)->toBeInstanceOf(Location::class);
    expect($location->fresh()->name)->toBe('Shelf A');
    expect($location->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::LocationUpdate,
    );
});

it('moves a location under a new parent', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $shelf = Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf A']);
    $box = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box 1']);

    new UpdateLocation(
        user: $owner,
        location: $box,
        name: 'Box 1',
        parentId: $shelf->id,
    )->execute();

    expect($box->fresh()->parent_id)->toBe($shelf->id);
});

it('throws when setting itself as its own parent', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $location = Location::factory()->create(['account_id' => $account->id]);

    new UpdateLocation(
        user: $owner,
        location: $location,
        name: 'Shelf A',
        parentId: $location->id,
    )->execute();
});

it('throws when nesting under one of its own descendants', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $shelf = Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf A']);
    $box = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box 1', 'parent_id' => $shelf->id]);

    new UpdateLocation(
        user: $owner,
        location: $shelf,
        name: 'Shelf A',
        parentId: $box->id,
    )->execute();
});

it('throws when the parent does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $location = Location::factory()->create(['account_id' => $account->id]);
    $foreignLocation = Location::factory()->create();

    new UpdateLocation(
        user: $owner,
        location: $location,
        name: 'Shelf A',
        parentId: $foreignLocation->id,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $location = Location::factory()->create(['account_id' => $account->id]);

    new UpdateLocation(
        user: $viewer,
        location: $location,
        name: 'Shelf A',
    )->execute();
});
