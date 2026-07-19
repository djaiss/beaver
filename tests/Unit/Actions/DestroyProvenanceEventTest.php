<?php

declare(strict_types=1);
use App\Actions\DestroyProvenanceEvent;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * An event on a copy sitting in a collection of the given user's account.
 */
function provenanceEventToDestroy(User $user): ProvenanceEvent
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
}

it('deletes a provenance event', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventToDestroy($ross);

    new DestroyProvenanceEvent(user: $ross, event: $event)->execute();

    $this->assertModelMissing($event);
});

// The event is the thing being removed, not the exchange behind it. A purchase
// still happened even once the story stops mentioning it.
it('leaves the transaction it came from alone', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventToDestroy($ross);
    $transaction = Transaction::factory()->create(['copy_id' => $event->copy_id]);
    $event->update(['transaction_id' => $transaction->id]);

    new DestroyProvenanceEvent(user: $ross, event: $event)->execute();

    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
});

it('logs the deletion against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventToDestroy($ross);

    new DestroyProvenanceEvent(user: $ross, event: $event)->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ProvenanceEventDeletion);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ProvenanceEventDeletion);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventToDestroy($ross);
    $gunther = $this->createUser();

    new DestroyProvenanceEvent(user: $gunther, event: $event)->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $event = provenanceEventToDestroy($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyProvenanceEvent(user: $phoebe, event: $event)->execute();
})->throws(ModelNotFoundException::class);
