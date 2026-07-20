<?php

declare(strict_types=1);
use App\Actions\MoveCopy;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\LocationHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function copyToMove(User $user): Copy
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('opens the first record and points the copy at it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);

    new MoveCopy(user: $ross, copy: $copy, location: $shelf, movedAt: '2024-01-01', reason: 'Catalogued')->execute();

    expect($copy->refresh()->current_location_id)->toBe($shelf->id);
    expect(LocationHistory::query()->where('copy_id', $copy->id)->count())->toBe(1);

    $open = $copy->openLocationHistory;
    expect($open->location_id)->toBe($shelf->id);
    expect($open->moved_out_at)->toBeNull();
    expect($open->reason)->toBe('Catalogued');
});

// The invariant: after a move there is exactly one open record, and it is the
// destination. The previous record is closed rather than left open.
it('closes the open record and opens a new one, keeping a single open record', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $safe = Location::factory()->create(['account_id' => $ross->account_id]);

    new MoveCopy(user: $ross, copy: $copy, location: $shelf, movedAt: '2024-01-01')->execute();
    new MoveCopy(user: $ross, copy: $copy, location: $safe, movedAt: '2024-06-01')->execute();

    $records = LocationHistory::query()->where('copy_id', $copy->id)->get();
    expect($records)->toHaveCount(2);
    expect($records->whereNull('moved_out_at'))->toHaveCount(1);

    $open = $copy->refresh()->openLocationHistory;
    expect($open->location_id)->toBe($safe->id);
    expect($copy->current_location_id)->toBe($safe->id);

    $closed = $records->firstWhere('location_id', $shelf->id);
    expect($closed->moved_out_at->toDateString())->toBe('2024-06-01');
});

it('does nothing when moving to the location the copy is already in', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);

    new MoveCopy(user: $ross, copy: $copy, location: $shelf, movedAt: '2024-01-01')->execute();
    new MoveCopy(user: $ross, copy: $copy, location: $shelf, movedAt: '2024-06-01')->execute();

    expect(LocationHistory::query()->where('copy_id', $copy->id)->count())->toBe(1);
});

it('logs the move against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);

    new MoveCopy(user: $ross, copy: $copy, location: $shelf, movedAt: '2024-01-01')->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::CopyMoved);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::CopyMoved);
});

it('refuses a location from another account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $foreign = Location::factory()->create(['account_id' => $this->createAccount()->id]);

    new MoveCopy(user: $ross, copy: $copy, location: $foreign, movedAt: '2024-01-01')->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyToMove($ross);
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new MoveCopy(user: $phoebe, copy: $copy, location: $shelf, movedAt: '2024-01-01')->execute();
})->throws(ModelNotFoundException::class);
