<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records a loan against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'status' => CopyStatus::Owned]);

    $response = $this->actingAs($user)->post(route('loans.create', [$collection, $item, $copy]), [
        'direction' => LoanDirection::Outgoing->value,
        'status' => LoanStatus::Active->value,
        'party' => 'The Whitney Museum',
        'loaned_at' => '2024-01-01',
        'due_at' => '2024-07-01',
        'deposit_amount' => '2500.00',
        'currency' => 'EUR',
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'loans']));
    $response->assertSessionHas('status', 'Loan recorded');

    $loan = Loan::query()->first();
    expect($loan->copy_id)->toBe($copy->id);
    expect($loan->direction)->toBe(LoanDirection::Outgoing);
    expect($loan->deposit_amount)->toBe(250000);
    expect($loan->deposit_currency_code)->toBe('EUR');
    expect($copy->refresh()->status)->toBe(CopyStatus::Loaned);
});

it('requires a direction, a party and a loaned date', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('loans.create', [$collection, $item, $copy]), [])
        ->assertSessionHasErrors(['direction', 'party', 'loaned_at']);
});

it('marks a loan as returned', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'status' => CopyStatus::Loaned]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'direction' => LoanDirection::Outgoing, 'status' => LoanStatus::Active]);
    $condition = Condition::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->put(route('loans.return.update', [$collection, $item, $copy, $loan]), [
        'returned_at' => '2024-06-01',
        'condition_in_id' => (string) $condition->id,
    ]);

    $response->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'loans']));
    $response->assertSessionHas('status', 'Loan marked as returned');

    expect($loan->refresh()->status)->toBe(LoanStatus::Returned);
    expect($copy->refresh()->status)->toBe(CopyStatus::Owned);
    expect($copy->condition_id)->toBe($condition->id);
});

it('updates a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'party' => 'Old party']);

    $this->actingAs($user)->put(route('loans.update', [$collection, $item, $copy, $loan]), [
        'direction' => LoanDirection::Outgoing->value,
        'status' => LoanStatus::Active->value,
        'party' => 'The Tate',
        'loaned_at' => '2024-01-01',
    ])->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'loans']));

    expect($loan->refresh()->party)->toBe('The Tate');
});

it('deletes a loan', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->delete(route('loans.destroy', [$collection, $item, $copy, $loan]))
        ->assertRedirect(route('items.history.show', [$collection, $item, $copy, 'loans']));

    $this->assertModelMissing($loan);
});

it('does not record a loan against a copy of another account', function () {
    $user = $this->createUser();
    $otherCollection = Collection::factory()->create(['account_id' => $this->createAccount()->id]);
    $otherItem = Item::factory()->create(['collection_id' => $otherCollection->id]);
    $otherCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)->post(route('loans.create', [$otherCollection, $otherItem, $otherCopy]), [
        'direction' => LoanDirection::Outgoing->value,
        'status' => LoanStatus::Active->value,
        'party' => 'A gallery',
        'loaned_at' => '2024-01-01',
    ])->assertNotFound();
});

it('forbids a viewer from recording a loan', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('loans.create', [$collection, $item, $copy]), [
        'direction' => LoanDirection::Outgoing->value,
        'status' => LoanStatus::Active->value,
        'party' => 'A gallery',
        'loaned_at' => '2024-01-01',
    ])->assertNotFound();
});
