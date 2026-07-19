<?php

declare(strict_types=1);
use App\Actions\CreateTransaction;
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
 * A copy sitting in a collection of the given user's account.
 */
function copyForTransaction(User $user, ?string $currency = 'USD'): Copy
{
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'currency' => $currency,
    ]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

it('records a transaction', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross);

    $transaction = new CreateTransaction(
        user: $ross,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
        counterparty: 'Central Perk Auctions',
        amount: 18000,
        taxAmount: 0,
        feeAmount: 2700,
        shippingAmount: 1800,
        referenceNumber: 'Lot #4021',
        note: 'Won at the January sale.',
    )->execute();

    expect($transaction)->toBeInstanceOf(Transaction::class);
    expect($transaction->type)->toBe(TransactionType::Purchase);
    expect($transaction->amount)->toBe(18000);
    expect($transaction->occurred_at->toDateString())->toBe('2026-01-08');
    expect($transaction->counterparty)->toBe('Central Perk Auctions');

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'copy_id' => $copy->id,
        'amount' => 18000,
        'fee_amount' => 2700,
    ]);
});

// Every amount on the row is in one currency, so the collection's is the
// sensible default rather than leaving it unset.
it('falls back to the currency of the collection', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross, 'EUR');

    $transaction = new CreateTransaction(
        user: $ross,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
        amount: 100,
    )->execute();

    expect($transaction->currency_code)->toBe('EUR');
});

it('keeps the currency it was given', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross, 'EUR');

    $transaction = new CreateTransaction(
        user: $ross,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
        amount: 100,
        currencyCode: 'GBP',
    )->execute();

    expect($transaction->currency_code)->toBe('GBP');
});

it('stamps who created it', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross);

    $transaction = new CreateTransaction(
        user: $ross,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
    )->execute();

    expect($transaction->created_by_id)->toBe($ross->id);
    expect($transaction->created_by_name)->toBe($ross->getFullName());
    expect($transaction->updated_by_id)->toBe($ross->id);
});

it('logs the action against the user and the item', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross);

    new CreateTransaction(
        user: $ross,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
    )->execute();

    Queue::assertPushedOn('low', LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::TransactionCreation);
    Queue::assertPushedOn('low', LogItemAction::class, fn (LogItemAction $job): bool => $job->action === ItemActionEnum::TransactionCreation);
});

it('throws when the user may not manage the account', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross);
    $gunther = $this->createUser();

    new CreateTransaction(
        user: $gunther,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
    )->execute();
})->throws(ModelNotFoundException::class);

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $ross = $this->createUser();
    $copy = copyForTransaction($ross);
    $phoebe = $this->createUser(['account_id' => $ross->account_id, 'role' => 'viewer']);

    new CreateTransaction(
        user: $phoebe,
        copy: $copy,
        type: TransactionType::Purchase,
        occurredAt: '2026-01-08',
    )->execute();
})->throws(ModelNotFoundException::class);
