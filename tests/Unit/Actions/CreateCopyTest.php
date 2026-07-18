<?php

declare(strict_types=1);
use App\Actions\CreateCopy;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a copy and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $copy = new CreateCopy(
        user: $editor,
        item: $item,
        condition: $condition,
        location: $location,
        acquiredAt: '2026-07-17',
        pricePaid: 4200,
        estimatedValue: 9900,
    )->execute();

    expect($copy)->toBeInstanceOf(Copy::class);
    expect($copy->item_id)->toBe($item->id);
    expect($copy->condition_id)->toBe($condition->id);
    expect($copy->location_id)->toBe($location->id);
    expect($copy->acquired_at->toDateString())->toBe('2026-07-17');
    expect($copy->price_paid)->toBe(4200);
    expect($copy->estimated_value)->toBe(9900);

    $this->assertDatabaseHas('copies', [
        'id' => $copy->id,
        'item_id' => $item->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($copy->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CopyCreation,
    );
});

it('creates a copy with only an item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $copy = new CreateCopy(
        user: $owner,
        item: $item,
    )->execute();

    expect($copy->condition_id)->toBeNull();
    expect($copy->location_id)->toBeNull();
    expect($copy->acquired_at)->toBeNull();
    expect($copy->price_paid)->toBeNull();
    expect($copy->estimated_value)->toBeNull();
});

it('throws when the condition belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignCondition = Condition::factory()->create();

    new CreateCopy(
        user: $owner,
        item: $item,
        condition: $foreignCondition,
    )->execute();
});

it('throws when the location belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignLocation = Location::factory()->create();

    new CreateCopy(
        user: $owner,
        item: $item,
        location: $foreignLocation,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new CreateCopy(
        user: $viewer,
        item: $item,
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new CreateCopy(
        user: $stranger,
        item: $item,
    )->execute();
});
