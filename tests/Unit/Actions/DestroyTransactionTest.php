<?php

declare(strict_types=1);
use App\Actions\DestroyTransaction;
use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

/**
 * A transaction on a copy sitting in a collection of the given user's account.
 */
function transactionToDestroy(User $user): Transaction
{
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return Transaction::factory()->create(['copy_id' => $copy->id]);
}

it('deletes a transaction', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionToDestroy($ross);

    new DestroyTransaction(user: $ross, transaction: $transaction)->execute();

    $this->assertModelMissing($transaction);
});

// The log is written before the row goes, so the entry can still say what was
// deleted rather than pointing at nothing.
it('logs the deletion against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionToDestroy($ross);

    new DestroyTransaction(user: $ross, transaction: $transaction)->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TransactionDeletion);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::TransactionDeletion);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionToDestroy($ross);
    $gunther = $this->createUser();

    new DestroyTransaction(user: $gunther, transaction: $transaction)->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionToDestroy($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new DestroyTransaction(user: $phoebe, transaction: $transaction)->execute();
})->throws(ModelNotFoundException::class);

it('leaves the transaction alone when the user may not delete it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionToDestroy($ross);
    $gunther = $this->createUser();

    try {
        new DestroyTransaction(user: $gunther, transaction: $transaction)->execute();
    } catch (ModelNotFoundException) {
        // The throw is asserted above; what matters here is that nothing went.
    }

    $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
});
