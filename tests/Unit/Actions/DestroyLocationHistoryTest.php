<?php

declare(strict_types=1);
use App\Actions\DestroyLocationHistory;
use App\Actions\MoveCopy;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function copyWithTwoMoves(User $user, Location $first, Location $second): Copy
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    new MoveCopy(user: $user, copy: $copy, location: $first, movedAt: '2024-01-01')->execute();
    new MoveCopy(user: $user, copy: $copy, location: $second, movedAt: '2024-06-01')->execute();

    return $copy->refresh();
}

it('deletes a closed record and leaves the pointer alone', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $safe = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithTwoMoves($ross, $shelf, $safe);

    $closed = $copy->locationHistory()->whereNotNull('moved_out_at')->first();

    new DestroyLocationHistory(user: $ross, record: $closed)->execute();

    $this->assertModelMissing($closed);
    expect($copy->refresh()->current_location_id)->toBe($safe->id);
});

it('nulls the copy location when the open record is deleted', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithTwoMoves($ross, $shelf, Location::factory()->create(['account_id' => $ross->account_id]));

    $open = $copy->openLocationHistory;

    new DestroyLocationHistory(user: $ross, record: $open)->execute();

    $this->assertModelMissing($open);
    expect($copy->refresh()->current_location_id)->toBeNull();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $shelf = Location::factory()->create(['account_id' => $ross->account_id]);
    $copy = copyWithTwoMoves($ross, $shelf, Location::factory()->create(['account_id' => $ross->account_id]));
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyLocationHistory(user: $phoebe, record: $copy->openLocationHistory)->execute();
})->throws(ModelNotFoundException::class);
