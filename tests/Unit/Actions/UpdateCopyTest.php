<?php

declare(strict_types=1);
use App\Actions\UpdateCopy;
use App\Enums\CopyStatus;
use App\Enums\ItemActionEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\LocationHistory;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a copy and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $copy = new UpdateCopy(
        user: $editor,
        copy: $copy,
        itemCondition: $condition,
        location: $location,
        identifier: 'CGC 1234567',
        status: CopyStatus::Sold,
        quantity: 3,
        disposedAt: '2026-07-17',
        note: 'Sold to Gunther.',
        estimatedValue: 9900,
    )->execute();

    expect($copy->item_condition_id)->toBe($condition->id);
    expect($copy->current_location_id)->toBe($location->id);
    expect($copy->identifier)->toBe('CGC 1234567');
    expect($copy->status)->toBe(CopyStatus::Sold);
    expect($copy->quantity)->toBe(3);
    expect($copy->disposed_at->toDateString())->toBe('2026-07-17');
    expect($copy->note)->toBe('Sold to Gunther.');
    expect($copy->estimatedValue())->toBe(9900);

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
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'item_condition_id' => $condition->id]);

    $copy = new UpdateCopy(
        user: $owner,
        copy: $copy,
    )->execute();

    expect($copy->item_condition_id)->toBeNull();
    expect($copy->current_location_id)->toBeNull();
});

// Valuations are append-only, so what a copy was worth survives being revalued.
it('appends a valuation when the estimated value moves and keeps the old one', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $first = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 39000,
        'valued_at' => '2026-07-01',
    ]);

    $copy = new UpdateCopy(
        user: $owner,
        copy: $copy,
        estimatedValue: 42000,
    )->execute();

    expect($copy->valuations()->count())->toBe(2);
    expect($copy->estimatedValue())->toBe(42000);
    expect($first->fresh()->amount)->toBe(39000);
});

it('does not append a valuation when the estimated value has not moved', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 42000]);

    $copy = new UpdateCopy(
        user: $owner,
        copy: $copy,
        estimatedValue: 42000,
    )->execute();

    expect($copy->valuations()->count())->toBe(1);
});

it('throws when the condition belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $foreignCondition = ItemCondition::factory()->create();

    new UpdateCopy(
        user: $owner,
        copy: $copy,
        itemCondition: $foreignCondition,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
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
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
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
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $wasIn = Location::factory()->create(['account_id' => $account->id, 'name' => 'Box A1']);
    $nowIn = Location::factory()->create(['account_id' => $account->id, 'name' => 'Display Case']);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'current_location_id' => $wasIn->id,
        'item_condition_id' => null,
    ]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 39000]);

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
    $catalog = Catalog::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $condition = ItemCondition::factory()->create(['account_id' => $account->id]);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'item_condition_id' => $condition->id,
        'current_location_id' => null,
    ]);

    new UpdateCopy(user: $editor, copy: $copy, itemCondition: $condition)->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogItemAction::class,
        callback: fn (LogItemAction $job): bool => $job->parameters === null,
    );
});

// Changing a copy's location goes through the move path, so the open record is
// closed and a new one opened rather than the pointer being overwritten alone.
it('moves the copy through its history when the location changes', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $shelf = Location::factory()->create(['account_id' => $user->account_id]);
    $safe = Location::factory()->create(['account_id' => $user->account_id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'current_location_id' => $shelf->id]);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $shelf->id, 'moved_at' => '2024-01-01', 'moved_out_at' => null]);

    new UpdateCopy(user: $user, copy: $copy, location: $safe)->execute();

    $records = LocationHistory::query()->where('copy_id', $copy->id)->get();
    expect($records)->toHaveCount(2);
    expect($records->whereNull('moved_out_at'))->toHaveCount(1);
    expect($copy->refresh()->current_location_id)->toBe($safe->id);
    expect($copy->openLocationHistory->location_id)->toBe($safe->id);
});
