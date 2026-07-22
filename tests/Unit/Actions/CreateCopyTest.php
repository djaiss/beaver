<?php

declare(strict_types=1);
use App\Actions\CreateCopy;
use App\Enums\CopyStatus;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\LocationHistory;
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
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $copy = new CreateCopy(
        user: $editor,
        item: $item,
        itemCondition: $condition,
        location: $location,
        identifier: 'CGC 1234567',
        status: CopyStatus::Loaned,
        quantity: 2,
        disposedAt: '2026-07-17',
        note: 'Lent to Joey.',
        estimatedValue: 9900,
    )->execute();

    expect($copy)->toBeInstanceOf(Copy::class);
    expect($copy->item_id)->toBe($item->id);
    expect($copy->item_condition_id)->toBe($condition->id);
    expect($copy->current_location_id)->toBe($location->id);
    expect($copy->identifier)->toBe('CGC 1234567');
    expect($copy->status)->toBe(CopyStatus::Loaned);
    expect($copy->quantity)->toBe(2);
    expect($copy->disposed_at->toDateString())->toBe('2026-07-17');
    expect($copy->note)->toBe('Lent to Joey.');
    expect($copy->estimatedValue())->toBe(9900);

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

    expect($copy->item_condition_id)->toBeNull();
    expect($copy->current_location_id)->toBeNull();
    expect($copy->identifier)->toBeNull();
    expect($copy->status)->toBe(CopyStatus::Owned);
    expect($copy->quantity)->toBe(1);
    expect($copy->disposed_at)->toBeNull();
    expect($copy->note)->toBeNull();
    expect($copy->estimatedValue())->toBeNull();
});

// The estimated value is no longer a column, so a figure given with the copy has
// to open its valuation history instead.
it('records the estimated value as a valuation rather than a column', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $copy = new CreateCopy(
        user: $owner,
        item: $item,
        estimatedValue: 42000,
    )->execute();

    expect($copy->valuations()->count())->toBe(1);

    $valuation = $copy->valuations()->first();

    expect($valuation->amount)->toBe(42000);
    expect($valuation->type)->toBe(ValuationType::UserEstimate);
    expect($valuation->currency_code)->toBe('USD');
    expect($valuation->confidence)->toBe(ValuationConfidence::Unknown);
    expect($valuation->valued_at->toDateString())->toBe(now()->toDateString());
    expect($valuation->created_by_id)->toBe($owner->id);
});

it('throws when the condition belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignCondition = ItemCondition::factory()->create();

    new CreateCopy(
        user: $owner,
        item: $item,
        itemCondition: $foreignCondition,
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

// Creating a copy somewhere goes through the move path, so it opens the copy's
// first location record rather than only setting the pointer.
it('opens the copy first location record when created somewhere', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);

    $copy = new CreateCopy(user: $user, item: $item, location: $location)->execute();

    $open = $copy->openLocationHistory;
    expect($open)->toBeInstanceOf(LocationHistory::class);
    expect($open->location_id)->toBe($location->id);
    expect($open->moved_out_at)->toBeNull();
    expect($copy->current_location_id)->toBe($location->id);
});
