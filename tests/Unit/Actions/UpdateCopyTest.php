<?php

declare(strict_types=1);
use App\Actions\UpdateCopy;
use App\Enums\ItemActionEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
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

it('updates a copy and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'price_paid' => 100]);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $copy = new UpdateCopy(
        user: $editor,
        copy: $copy,
        condition: $condition,
        location: $location,
        acquiredAt: '2026-07-17',
        pricePaid: 4200,
        estimatedValue: 9900,
    )->execute();

    expect($copy->condition_id)->toBe($condition->id);
    expect($copy->location_id)->toBe($location->id);
    expect($copy->acquired_at->toDateString())->toBe('2026-07-17');
    expect($copy->price_paid)->toBe(4200);
    expect($copy->estimated_value)->toBe(9900);

    $this->assertDatabaseHas('copies', [
        'id' => $copy->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($copy->updated_by_name)->toBe('Monica Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CopyUpdate,
    );
});

it('clears the condition and location when none are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'condition_id' => $condition->id]);

    $copy = new UpdateCopy(
        user: $owner,
        copy: $copy,
    )->execute();

    expect($copy->condition_id)->toBeNull();
    expect($copy->location_id)->toBeNull();
});

it('throws when the condition belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $foreignCondition = Condition::factory()->create();

    new UpdateCopy(
        user: $owner,
        copy: $copy,
        condition: $foreignCondition,
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
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    new UpdateCopy(
        user: $viewer,
        copy: $copy,
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    new UpdateCopy(
        user: $stranger,
        copy: $copy,
    )->execute();
});

it('records the values that moved on the activity of the item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $wasIn = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box A1']);
    $nowIn = Location::factory()->create(['account_id' => $account->id, 'name' => 'Display Case']);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'location_id' => $wasIn->id,
        'condition_id' => null,
        'acquired_at' => null,
        'price_paid' => null,
        'estimated_value' => 39000,
    ]);

    new UpdateCopy(
        user: $editor,
        copy: $copy,
        location: $nowIn,
        estimatedValue: 42000,
    )->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogItemAction::class,
        callback: fn (LogItemAction $job): bool => $job->action === ItemActionEnum::CopyUpdate
            && $job->parameters === ['changes' => [
                ['label' => 'Location', 'from' => 'Box A1', 'to' => 'Display Case'],
                ['label' => 'Estimated value', 'from' => '$390', 'to' => '$420'],
            ]],
    );
});

it('records no chips when nothing on the copy moved', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'condition_id' => $condition->id,
        'location_id' => null,
        'acquired_at' => null,
        'price_paid' => null,
        'estimated_value' => null,
    ]);

    new UpdateCopy(user: $editor, copy: $copy, condition: $condition)->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogItemAction::class,
        callback: fn (LogItemAction $job): bool => $job->parameters === null,
    );
});
