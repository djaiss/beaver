<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Enums\TransactionType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records a transaction against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
        'counterparty' => 'Central Perk Comics',
        'amount' => '120.50',
        'currency' => 'EUR',
        'tax_amount' => '10',
        'fee_amount' => '2.25',
        'shipping_amount' => '5',
        'reference_number' => 'Invoice 4021',
        'note' => 'Bought the day Ross said we were on a break.',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'transactions']));
    $response->assertSessionHas('status', 'Transaction recorded');

    $transaction = Transaction::query()->first();
    expect($transaction->copy_id)->toBe($copy->id);
    expect($transaction->type)->toBe(TransactionType::Purchase);
    expect($transaction->counterparty)->toBe('Central Perk Comics');
    expect($transaction->currency_code)->toBe('EUR');
    expect($transaction->reference_number)->toBe('Invoice 4021');
});

// The form collects money in currency units, and everything is stored in cents.
it('converts every amount from currency units to cents', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
        'amount' => '120.50',
        'tax_amount' => '10',
        'fee_amount' => '2.25',
        'shipping_amount' => '5',
        'total_amount' => '137.75',
    ]);

    $transaction = Transaction::query()->first();
    expect($transaction->amount)->toBe(12050);
    expect($transaction->tax_amount)->toBe(1000);
    expect($transaction->fee_amount)->toBe(225);
    expect($transaction->shipping_amount)->toBe(500);
    expect($transaction->total_amount)->toBe(13775);
});

it('leaves the amounts empty when none were given', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::GiftReceived->value,
        'occurred_at' => '2026-03-14',
    ]);

    $transaction = Transaction::query()->first();
    expect($transaction->amount)->toBeNull();
    expect($transaction->total_amount)->toBeNull();
    expect($transaction->total())->toBeNull();
});

// Every amount on a transaction is in one currency, and the collection's is the
// sensible default when the form does not say.
it('falls back to the currency of the collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'GBP']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
        'amount' => '10',
    ]);

    expect(Transaction::query()->first()->currency_code)->toBe('GBP');
});

it('validates the type and the date when recording a transaction', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [])
        ->assertSessionHasErrors(['type', 'occurred_at']);
});

it('rejects an unknown transaction type', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => 'bartering-for-a-monkey',
        'occurred_at' => '2026-03-14',
    ])->assertSessionHasErrors('type');
});

it('rejects a negative amount and an unknown currency', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
        'amount' => '-5',
        'currency' => 'ZZZ',
    ])->assertSessionHasErrors(['amount', 'currency']);
});

it('forbids a viewer from recording a transaction', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
    ])->assertNotFound();
});

it('does not record a transaction against a copy of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('transactions.create', [$foreign, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
    ])->assertNotFound();
});

it('does not record a transaction against a copy of another item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $other = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $other->id]);

    $this->actingAs($user)->post(route('transactions.create', [$collection, $item, $copy]), [
        'type' => TransactionType::Purchase->value,
        'occurred_at' => '2026-03-14',
    ])->assertNotFound();
});

it('updates a transaction', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'amount' => 1000,
        'occurred_at' => '2024-01-01',
    ]);

    $response = $this->actingAs($user)->put(route('transactions.update', [$collection, $item, $copy, $transaction]), [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2026-05-02',
        'counterparty' => 'Gunther',
        'amount' => '350',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'transactions']));
    $response->assertSessionHas('status', 'Transaction updated');

    $transaction->refresh();
    expect($transaction->type)->toBe(TransactionType::Sale);
    expect($transaction->amount)->toBe(35000);
    expect($transaction->counterparty)->toBe('Gunther');
    expect($transaction->occurred_at->toDateString())->toBe('2026-05-02');
});

it('validates the type and the date when updating a transaction', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->put(route('transactions.update', [$collection, $item, $copy, $transaction]), [])
        ->assertSessionHasErrors(['type', 'occurred_at']);
});

it('forbids a viewer from updating a transaction', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->put(route('transactions.update', [$collection, $item, $copy, $transaction]), [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2026-05-02',
    ])->assertNotFound();
});

it('does not update a transaction of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->put(route('transactions.update', [$foreign, $item, $copy, $transaction]), [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2026-05-02',
    ])->assertNotFound();
});

it('does not update a transaction that belongs to another copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $other = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $other->id]);

    $this->actingAs($user)->put(route('transactions.update', [$collection, $item, $copy, $transaction]), [
        'type' => TransactionType::Sale->value,
        'occurred_at' => '2026-05-02',
    ])->assertNotFound();
});

it('deletes a transaction', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('transactions.destroy', [$collection, $item, $copy, $transaction]));

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'section' => 'transactions']));
    $response->assertSessionHas('status', 'Transaction deleted');
    $this->assertModelMissing($transaction);
});

it('forbids a viewer from deleting a transaction', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('transactions.destroy', [$collection, $item, $copy, $transaction]))
        ->assertNotFound();

    $this->assertModelExists($transaction);
});

it('does not delete a transaction of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->delete(route('transactions.destroy', [$foreign, $item, $copy, $transaction]))
        ->assertNotFound();

    $this->assertModelExists($transaction);
});
