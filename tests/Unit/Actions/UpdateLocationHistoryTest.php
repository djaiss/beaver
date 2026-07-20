<?php

declare(strict_types=1);
use App\Actions\MoveCopy;
use App\Actions\UpdateLocationHistory;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function copyWithOpenRecord(User $user, Location $location): Copy
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    new MoveCopy(user: $user, copy: $copy, location: $location, movedAt: '2024-01-01')->execute();

    return $copy->refresh();
}

it('corrects a record', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithOpenRecord($ross, $shelf);
    $record = $copy->openLocationHistory;

    new UpdateLocationHistory(
        user: $ross,
        record: $record,
        location: $shelf,
        movedAt: '2024-02-15',
        reason: 'Corrected date',
    )->execute();

    $record->refresh();
    expect($record->moved_at->toDateString())->toBe('2024-02-15');
    expect($record->reason)->toBe('Corrected date');
});

it('re-points the copy when the open record location is corrected', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $safe = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithOpenRecord($ross, $shelf);
    $record = $copy->openLocationHistory;

    new UpdateLocationHistory(
        user: $ross,
        record: $record,
        location: $safe,
        movedAt: '2024-01-01',
    )->execute();

    expect($copy->refresh()->current_location_id)->toBe($safe->id);
});

it('refuses a location from another account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithOpenRecord($ross, $shelf);
    $foreign = Location::factory()->create(['account_id' => $this->createAccount()->id]);

    new UpdateLocationHistory(
        user: $ross,
        record: $copy->openLocationHistory,
        location: $foreign,
        movedAt: '2024-01-01',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithOpenRecord($ross, $shelf);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new UpdateLocationHistory(
        user: $phoebe,
        record: $copy->openLocationHistory,
        location: $shelf,
        movedAt: '2024-01-01',
    )->execute();
})->throws(ModelNotFoundException::class);
