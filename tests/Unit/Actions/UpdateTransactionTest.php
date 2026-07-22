<?php

declare(strict_types=1);
use App\Actions\UpdateTransaction;
use App\Enums\ItemActionEnum;
use App\Enums\TransactionType;
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
function transactionFor(User $user, array $attributes = []): Transaction
{
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'currency' => 'USD',
    ]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    return Transaction::factory()->create(['copy_id' => $copy->id, ...$attributes]);
}

it('updates a transaction', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross, ['type' => TransactionType::Purchase, 'amount' => 100]);

    $updated = new UpdateTransaction(
        user: $ross,
        transaction: $transaction,
        type: TransactionType::Sale,
        occurredAt: '2026-02-02',
        counterparty: 'Gunther',
        amount: 25000,
        feeAmount: 500,
    )->execute();

    expect($updated->type)->toBe(TransactionType::Sale);
    expect($updated->amount)->toBe(25000);
    expect($updated->fee_amount)->toBe(500);
    expect($updated->counterparty)->toBe('Gunther');
    expect($updated->occurred_at->toDateString())->toBe('2026-02-02');
});

// A field the caller leaves out is cleared rather than kept, since the action
// takes the whole shape of the transaction rather than a patch.
it('clears the amounts it is not given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross, ['amount' => 100, 'fee_amount' => 50]);

    $updated = new UpdateTransaction(
        user: $ross,
        transaction: $transaction,
        type: TransactionType::Purchase,
        occurredAt: '2026-02-02',
    )->execute();

    expect($updated->amount)->toBeNull();
    expect($updated->fee_amount)->toBeNull();
});

it('keeps the currency it already had when none is given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross, ['currency_code' => 'GBP']);

    $updated = new UpdateTransaction(
        user: $ross,
        transaction: $transaction,
        type: TransactionType::Purchase,
        occurredAt: '2026-02-02',
    )->execute();

    expect($updated->currency_code)->toBe('GBP');
});

it('stamps who updated it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross);
    $rachel = $this->createUser(['account_id' => $ross->account_id]);

    $updated = new UpdateTransaction(
        user: $rachel,
        transaction: $transaction,
        type: TransactionType::Purchase,
        occurredAt: '2026-02-02',
    )->execute();

    expect($updated->updated_by_id)->toBe($rachel->id);
    expect($updated->updated_by_name)->toBe($rachel->getFullName());
});

it('records what moved in the item log', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross, ['type' => TransactionType::Purchase, 'amount' => 100]);

    new UpdateTransaction(
        user: $ross,
        transaction: $transaction,
        type: TransactionType::Sale,
        occurredAt: $transaction->occurred_at->toDateString(),
        amount: 100,
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TransactionUpdate);
    Queue::assertPushedOn('low', LogItemAction::class, function (LogItemAction $job): bool {
        $labels = array_column($job->parameters['changes'] ?? [], 'label');

        return $job->action === ItemActionEnum::TransactionUpdate
            && in_array('Type', $labels, true)
            && ! in_array('Amount', $labels, true);
    });
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross);
    $gunther = $this->createUser();

    new UpdateTransaction(
        user: $gunther,
        transaction: $transaction,
        type: TransactionType::Purchase,
        occurredAt: '2026-02-02',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $transaction = transactionFor($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new UpdateTransaction(
        user: $phoebe,
        transaction: $transaction,
        type: TransactionType::Purchase,
        occurredAt: '2026-02-02',
    )->execute();
})->throws(ModelNotFoundException::class);
