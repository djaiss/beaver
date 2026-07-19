<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\DatePrecision;
use App\Enums\PermissionEnum;
use App\Enums\ProvenanceEventType;
use App\Enums\TransactionType;
use App\Enums\ValuationType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('lands on the first copy and shows its summary', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create([
        'item_id' => $item->id,
        'identifier' => 'CENTRAL-PERK-01',
        'status' => CopyStatus::Loaned,
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-copy-'.$copy->id.'"', false)
        ->assertSee('data-test="history-summary"', false)
        ->assertSee('CENTRAL-PERK-01')
        ->assertSee('Loaned out');
});

// The copy lives in the url, so every copy is offered as a pill and the chosen
// one is marked current.
it('offers a pill for every copy and marks the selected one', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $first = Copy::factory()->create(['item_id' => $item->id]);
    $second = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $second]))
        ->assertOk()
        ->assertSee('data-test="history-copy-pill-'.$first->id.'"', false)
        ->assertSee('data-test="history-copy-pill-'.$second->id.'"', false)
        // The selected copy's container is the second one.
        ->assertSee('data-test="history-copy-'.$second->id.'"', false)
        ->assertDontSee('data-test="history-copy-'.$first->id.'"', false);
});

it('marks the history tab as the current page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-history"', false)
        ->assertSee('aria-current="page"', false);
});

// The sections are listed rather than hidden until they are built, so the shape
// of the screen says what the history will be assembled from.
it('lists the sections the history is assembled from', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-sections"', false)
        ->assertSeeInOrder([
            'Timeline',
            'Transactions',
            'Valuations',
            'Provenance',
            'Insurance',
            'Maintenance',
            'Loans',
            'Locations',
            'Documents',
        ]);
});

it('shows the valuations of a copy on its timeline, oldest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $newer = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 25000,
        'valued_at' => '2026-03-01',
        'type' => ValuationType::ProfessionalAppraisal,
    ]);
    $older = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 10000,
        'valued_at' => '2024-01-01',
        'type' => ValuationType::UserEstimate,
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-valuation-'.$older->id.'"', false)
        ->assertSee('data-test="history-valuation-'.$newer->id.'"', false)
        ->assertSeeInOrder(['Valued at $100', 'Own estimate', 'Valued at $250', 'Professional appraisal'])
        ->assertSeeInOrder(['Jan 2024', 'Mar 2026']);
});

// Each section is its own url, so the valuations section renders its own panel
// while the copy stays the same.
it('shows the valuations section when it is selected', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 5000]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'valuations']))
        ->assertOk()
        ->assertSee('Append-only value estimates over time. The latest is shown as the current estimated value.')
        ->assertSee('data-test="valuation-'.$valuation->id.'"', false);
});

// A section with no screen yet still appears in the nav, and its content says so
// rather than showing nothing.
it('shows a placeholder for a section that is not built yet', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'insurance']))
        ->assertOk()
        ->assertSee('data-test="history-section-soon"', false)
        ->assertSee('This part of the history is not built yet.');
});

// A section the url invents is not trusted; it falls back to the timeline rather
// than erroring.
it('falls back to the timeline for an unknown section', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'nonsense']))
        ->assertOk()
        ->assertSee('Everything that has happened to this copy, oldest first. The sections listed alongside are what it is assembled from.');
});

it('shows the empty state when nothing has been recorded against a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-history"', false)
        ->assertSee('Nothing has been recorded against this copy yet.');
});

it('shows the empty state when the item has no copies at all', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-copies-to-track"', false)
        ->assertSee('This item has no copies, so there is nothing to track the history of.')
        ->assertDontSee('data-test="no-history"', false);
});

// A copy named in the url has to be one of this item's own.
it('does not show a copy that belongs to another item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $otherItem = Item::factory()->create(['collection_id' => $collection->id]);
    $strangerCopy = Copy::factory()->create(['item_id' => $otherItem->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $strangerCopy]))
        ->assertNotFound();
});

it('lets a viewer read the history of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($viewer)->get(route('items.history.index', [$collection, $item]))->assertOk();
});

it('does not show the history of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.history.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the history of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))->assertNotFound();
});

it('lists the transactions of a copy, newest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $older = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'counterparty' => 'Central Perk Comics',
        'amount' => 10000,
        'total_amount' => 10000,
        'currency_code' => 'USD',
        'occurred_at' => '2024-01-05',
    ]);
    $newer = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Sale,
        'counterparty' => 'Gunther',
        'amount' => 25000,
        'total_amount' => 25000,
        'currency_code' => 'USD',
        'occurred_at' => '2026-02-11',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="transaction-'.$older->id.'"', false)
        ->assertSee('data-test="transaction-'.$newer->id.'"', false)
        ->assertSeeInOrder(['Gunther', 'Feb 11, 2026', 'Central Perk Comics', 'Jan 5, 2024'])
        ->assertSee('$250')
        ->assertSee('$100');
});

// The stored total is optional, so the headline figure has to be the sum of the
// parts when nobody typed one.
it('adds the parts together when a transaction has no stored total', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 10000,
        'tax_amount' => 900,
        'fee_amount' => 250,
        'shipping_amount' => 850,
        'total_amount' => null,
        'currency_code' => 'USD',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="transaction-total-'.$transaction->id.'"', false)
        ->assertSee('$120')
        ->assertSeeInOrder(['Amount', 'Tax', 'Fees', 'Shipping']);
});

it('shows the reference number and the note of a transaction', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'reference_number' => 'Invoice 4021',
        'note' => 'Bought the day Ross said we were on a break.',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="transaction-reference-'.$transaction->id.'"', false)
        ->assertSee('Invoice 4021')
        ->assertSee('Bought the day Ross said we were on a break.');
});

it('says so when a copy has no transaction yet', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="no-transactions-'.$copy->id.'"', false)
        ->assertSee('No transaction has been recorded against this copy yet.');
});

it('offers an editor the forms to add, edit and delete a transaction', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="new-transaction-'.$copy->id.'"', false)
        ->assertSee('data-test="create-transaction-form-'.$copy->id.'"', false)
        ->assertSee('data-test="edit-transaction-form-'.$transaction->id.'"', false)
        ->assertSee('data-test="delete-transaction-'.$transaction->id.'"', false)
        ->assertSee(route('transactions.create', [$collection, $item, $copy]), false);
});

// Deleting a transaction cannot be undone, so it has to be confirmed first.
it('asks for confirmation before deleting a transaction', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('Delete this transaction? This cannot be undone.');
});

it('does not offer a viewer any way to change a transaction', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="transaction-'.$transaction->id.'"', false)
        ->assertDontSee('data-test="new-transaction-'.$copy->id.'"', false)
        ->assertDontSee('data-test="edit-transaction-'.$transaction->id.'"', false)
        ->assertDontSee('data-test="delete-transaction-'.$transaction->id.'"', false);
});

// Provenance reads as a narrative rather than as a feed, so unlike the
// transactions the timeline runs forwards.
it('lists the provenance of a copy on a timeline, oldest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $newer = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'type' => ProvenanceEventType::Exhibition,
        'title' => 'Shown at the museum',
        'occurred_at' => '2001-04-09',
        'occurred_at_precision' => DatePrecision::Exact,
    ]);
    $older = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'type' => ProvenanceEventType::Acquisition,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Exact,
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="provenance-event-'.$older->id.'"', false)
        ->assertSee('data-test="provenance-event-'.$newer->id.'"', false)
        ->assertSeeInOrder([
            'Bought at the Central Perk auction',
            'Shown at the museum',
        ]);
});

// Showing the stored day would claim a precision the evidence does not support.
it('renders a provenance date at the precision it was recorded at', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $year = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Left the factory',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Year,
    ]);
    $undated = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Changed hands at some point',
        'occurred_at' => null,
        'occurred_at_precision' => DatePrecision::Unknown,
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="provenance-event-date-'.$year->id.'"', false)
        ->assertSee('1987')
        ->assertDontSee('June 2, 1987')
        ->assertSee('data-test="provenance-event-full-date-'.$undated->id.'"', false)
        ->assertSee('Date unknown');
});

it('shows the parties, the location and the verified badge of a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Bought at the Central Perk auction',
        'from_party' => 'Gunther',
        'to_party' => 'Ross Geller',
        'location' => 'New York',
        'reference_number' => 'Lot 118',
        'is_verified' => true,
        'verification_note' => 'Checked against the auction catalogue.',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="provenance-event-parties-'.$event->id.'"', false)
        ->assertSee('From Gunther to Ross Geller')
        ->assertSee('data-test="provenance-event-location-'.$event->id.'"', false)
        ->assertSee('New York')
        ->assertSee('data-test="provenance-event-reference-'.$event->id.'"', false)
        ->assertSee('Lot 118')
        ->assertSee('data-test="provenance-event-verified-'.$event->id.'"', false)
        ->assertSee('Checked against the auction catalogue.');
});

// Deleting the transaction unlinks the event rather than deleting it, and the
// screen has to say so before anyone deletes anything.
it('says when a provenance event is linked to a transaction, and on the transaction too', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'occurred_at' => '1987-06-02',
    ]);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'transaction_id' => $transaction->id,
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="provenance-event-transaction-'.$event->id.'"', false)
        ->assertSee('Deleting that transaction keeps this event and only unlinks it.');

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('data-test="transaction-provenance-'.$transaction->id.'"', false)
        ->assertSee('In the provenance')
        ->assertSee('Its provenance event is kept and simply unlinked.');
});

it('says so when a copy has no provenance event yet', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="no-provenance-'.$copy->id.'"', false)
        ->assertSee('No provenance event has been recorded against this copy yet.');
});

it('offers an editor the forms to add, edit and delete a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="new-provenance-event-'.$copy->id.'"', false)
        ->assertSee('data-test="create-provenance-event-form-'.$copy->id.'"', false)
        ->assertSee('data-test="edit-provenance-event-form-'.$event->id.'"', false)
        ->assertSee('data-test="delete-provenance-event-'.$event->id.'"', false)
        ->assertSee(route('provenanceEvents.create', [$collection, $item, $copy]), false);
});

// Deleting a provenance event cannot be undone, so it has to be confirmed first.
it('asks for confirmation before deleting a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('Delete this provenance event? This cannot be undone.');
});

it('does not offer a viewer any way to change a provenance event', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('data-test="provenance-event-'.$event->id.'"', false)
        ->assertDontSee('data-test="new-provenance-event-'.$copy->id.'"', false)
        ->assertDontSee('data-test="edit-provenance-event-'.$event->id.'"', false)
        ->assertDontSee('data-test="delete-provenance-event-'.$event->id.'"', false);
});

// The precision decides how the date reads, and each one needs explaining.
it('offers every provenance type, every precision and the copy transactions on the form', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Purchase,
        'occurred_at' => '1987-06-02',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'provenance']))
        ->assertOk()
        ->assertSee('Significant restoration')
        ->assertSee('Custody transfer')
        ->assertSee('Approximate')
        ->assertSee('Only the year is known.')
        ->assertSee('Not linked to a transaction')
        ->assertSee('<option value="'.$transaction->id.'"', false);
});

it('offers every transaction type and the currency of the collection on the form', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'GBP']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'transactions']))
        ->assertOk()
        ->assertSee('Gift received')
        ->assertSee('Inheritance')
        ->assertSee('<option value="GBP" selected>', false);
});
