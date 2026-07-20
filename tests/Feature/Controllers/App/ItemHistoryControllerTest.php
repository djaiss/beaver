<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\DatePrecision;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\MaintenanceType;
use App\Enums\PermissionEnum;
use App\Enums\ProvenanceEventType;
use App\Enums\TransactionType;
use App\Enums\ValuationType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Document;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Location;
use App\Models\LocationHistory;
use App\Models\MaintenanceRecord;
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

it('shows the valuations of a copy on its timeline, newest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $newer = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 25000,
        'currency_code' => 'USD',
        'valued_at' => '2026-03-01',
        'type' => ValuationType::ProfessionalAppraisal,
    ]);
    $older = Valuation::factory()->create([
        'copy_id' => $copy->id,
        'amount' => 10000,
        'currency_code' => 'USD',
        'valued_at' => '2024-01-01',
        'type' => ValuationType::UserEstimate,
    ]);

    // The amount renders in its own currency, and the newest entry reads first.
    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-valuation-'.$older->id.'"', false)
        ->assertSee('data-test="history-valuation-'.$newer->id.'"', false)
        ->assertSeeInOrder(['Professional appraisal', '$250', 'Own estimate', '$100'])
        ->assertSeeInOrder(['Mar 1, 2026', 'Jan 1, 2024']);
});

// The default view is the meaningful one: a purchase and a valuation belong to
// the object's story, while a fee, a routine cleaning and an ordinary move are
// operational and stay out until the complete view is asked for.
it('shows only the meaningful entries by default', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-05-01']);
    $fee = Transaction::factory()->create([
        'copy_id' => $copy->id,
        'type' => TransactionType::Fee,
        'occurred_at' => '2012-06-01',
    ]);
    $routineCleaning = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'type' => MaintenanceType::Cleaning,
        'include_in_provenance' => false,
        'performed_at' => '2012-07-01',
    ]);
    $location = Location::factory()->create(['account_id' => $user->account_id]);
    $move = LocationHistory::factory()->create([
        'copy_id' => $copy->id,
        'location_id' => $location->id,
        'moved_at' => '2012-08-01',
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-valuation-'.$valuation->id.'"', false)
        ->assertDontSee('data-test="history-transaction-'.$fee->id.'"', false)
        ->assertDontSee('data-test="history-maintenance-'.$routineCleaning->id.'"', false)
        ->assertDontSee('data-test="history-location-'.$move->id.'"', false);
});

// The complete view adds the routine records the meaningful view leaves out.
it('shows the routine entries in the complete view', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Secure storage']);
    $move = LocationHistory::factory()->create([
        'copy_id' => $copy->id,
        'location_id' => $location->id,
        'moved_at' => '2026-01-01',
    ]);
    $routineCleaning = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'type' => MaintenanceType::Cleaning,
        'include_in_provenance' => false,
        'performed_at' => '2025-01-01',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'timeline', 'view' => 'complete']))
        ->assertOk()
        ->assertSee('data-test="history-location-'.$move->id.'"', false)
        ->assertSee('Moved to Secure storage')
        ->assertSee('data-test="history-maintenance-'.$routineCleaning->id.'"', false)
        ->assertSee('data-test="timeline-view-complete"', false);
});

// A conservation or a restoration is meaningful even when it is not flagged, so
// it reads on the default timeline while a plain cleaning does not.
it('treats a restoration as meaningful without the flag', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $restoration = MaintenanceRecord::factory()->create([
        'copy_id' => $copy->id,
        'type' => MaintenanceType::Restoration,
        'include_in_provenance' => false,
        'title' => 'Full restoration',
        'performed_at' => '1998-01-01',
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-maintenance-'.$restoration->id.'"', false);
});

// The type filter narrows the timeline to chosen sources, and offers a chip only
// for the sources the copy actually has.
it('filters the timeline by event type', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Acquired at auction',
        'occurred_at' => '1987-01-01',
        'occurred_at_precision' => DatePrecision::Year,
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'timeline', 'type' => ['valuation']]))
        ->assertOk()
        ->assertSee('data-test="history-valuation-'.$valuation->id.'"', false)
        ->assertDontSee('data-test="history-provenance-'.$event->id.'"', false)
        // Both sources are offered as chips, meaningful or filtered out or not.
        ->assertSee('data-test="timeline-type-valuation"', false)
        ->assertSee('data-test="timeline-type-provenance"', false);
});

// The merge runs across every source, newest first, so a recent valuation reads
// above an older acquisition regardless of which model each came from.
it('orders entries from every source newest first', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    $acquisition = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Acquired from the gallery',
        'occurred_at' => '1987-01-01',
        'occurred_at_precision' => DatePrecision::Year,
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSeeInOrder([
            'data-test="history-valuation-'.$valuation->id.'"',
            'data-test="history-provenance-'.$acquisition->id.'"',
        ], false);
});

// An undated provenance event cannot claim a spot on the line, so it drops below
// the dated entries rather than reading as though it happened at the epoch.
it('sorts undated entries to the end', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $dated = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    $undated = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'title' => 'Origin unknown',
        'occurred_at' => null,
        'occurred_at_precision' => DatePrecision::Unknown,
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSeeInOrder([
            'data-test="history-valuation-'.$dated->id.'"',
            'data-test="history-provenance-'.$undated->id.'"',
        ], false);
});

// A returned loan leaves the object twice: once going out, once coming back, so
// it reads as two entries.
it('shows a loan and its return as separate entries', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'direction' => LoanDirection::Outgoing,
        'status' => LoanStatus::Returned,
        'party' => 'The Louvre',
        'include_in_provenance' => true,
        'loaned_at' => '2025-01-01',
        'returned_at' => '2025-06-01',
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-loan-'.$loan->id.'"', false)
        ->assertSee('data-test="history-loan-'.$loan->id.'-return"', false)
        ->assertSee('Loaned to The Louvre')
        ->assertSee('Returned from The Louvre');
});

// Each entry links back into the section it came from, so a valuation on the
// timeline opens the valuations panel for the full record.
it('links each entry into its section', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee(route('items.history.show', [$collection, $item, $copy, 'valuations']), false);
});

// A copy that has records but none matching the filter reads a filter empty
// state rather than the no-history one, so the reader knows there is more.
it('shows the filter empty state when nothing matches the current view', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    // A single routine move: nothing meaningful, so the default view is empty
    // while the copy still has history.
    $location = Location::factory()->create(['account_id' => $user->account_id]);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $location->id]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="no-history-matches"', false)
        ->assertDontSee('data-test="no-history"', false);
});

// Insurance opening reads as a meaningful entry with the insured value in its own
// currency.
it('shows an insurance record on the timeline', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $insurance = InsuranceRecord::factory()->create([
        'copy_id' => $copy->id,
        'provider' => 'Hiscox',
        'insured_value' => 450000,
        'currency_code' => 'GBP',
        'starts_at' => '2022-01-01',
    ]);

    $this->actingAs($user)->get(route('items.history.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="history-insurance-'.$insurance->id.'"', false)
        ->assertSee('Hiscox')
        ->assertSee('£4,500');
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

// The documents section reads the documents attached to the copy as a whole.
it('shows the documents section with the copy documents', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    Document::factory()->for($copy, 'documentable')->create([
        'account_id' => $user->account_id,
        'name' => 'Certificate of authenticity',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'documents']))
        ->assertOk()
        ->assertSee('data-test="documents-for-copy-'.$copy->id.'"', false)
        ->assertSee('Certificate of authenticity');
});

it('renders the loans section with the copy loans', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $loan = Loan::factory()->create(['copy_id' => $copy->id, 'party' => 'The Whitney Museum']);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'loans']))
        ->assertOk()
        ->assertSee('data-test="loan-'.$loan->id.'"', false)
        ->assertSee('The Whitney Museum');
});

it('shows the active loan banner while an outgoing loan is out', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id, 'status' => CopyStatus::Loaned]);
    Loan::factory()->create([
        'copy_id' => $copy->id,
        'direction' => LoanDirection::Outgoing,
        'status' => LoanStatus::Active,
        'party' => 'The Tate',
    ]);

    $this->actingAs($user)->get(route('items.history.show', [$collection, $item, $copy, 'timeline']))
        ->assertOk()
        ->assertSee('data-test="loan-banner-'.$copy->id.'"', false)
        ->assertSee('The Tate');
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
        ->assertSee('A combined chronological view built from every record below. Each entry keeps its own source of truth.');
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
