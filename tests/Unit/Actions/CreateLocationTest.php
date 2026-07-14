<?php

declare(strict_types=1);
use App\Actions\CreateLocation;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a location and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $location = new CreateLocation(
        user: $editor,
        account: $account,
        name: 'Shelf A',
    )->execute();

    expect($location)->toBeInstanceOf(Location::class);
    expect($location->name)->toBe('Shelf A');
    expect($location->account_id)->toBe($account->id);
    expect($location->parent_id)->toBeNull();

    $this->assertDatabaseHas('locations', [
        'id' => $location->id,
        'account_id' => $account->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($location->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::LocationCreation,
    );
});

it('creates a location with an emoji', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $location = new CreateLocation(
        user: $owner,
        account: $account,
        name: 'Garage',
        emoji: '🚪',
    )->execute();

    expect($location->emoji)->toBe('🚪');
});

it('creates a nested location', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $shelf = Location::factory()->create(['account_id' => $account->id, 'name' => 'Shelf A']);

    $box = new CreateLocation(
        user: $owner,
        account: $account,
        name: 'Box 1',
        parentId: $shelf->id,
    )->execute();

    expect($box->parent_id)->toBe($shelf->id);
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $location = new CreateLocation(
        user: $owner,
        account: $account,
        name: '<strong>Shelf A</strong>',
    )->execute();

    expect($location->name)->toBe('Shelf A');
});

it('throws when the parent does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $foreignLocation = Location::factory()->create();

    new CreateLocation(
        user: $owner,
        account: $account,
        name: 'Box 1',
        parentId: $foreignLocation->id,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    new CreateLocation(
        user: $viewer,
        account: $account,
        name: 'Shelf A',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();

    new CreateLocation(
        user: $stranger,
        account: $account,
        name: 'Shelf A',
    )->execute();
});
