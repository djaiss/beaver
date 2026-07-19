<?php

declare(strict_types=1);
use App\Enums\DatePrecision;
use App\Enums\PermissionEnum;
use App\Enums\ProvenanceEventType;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('records a provenance event against a copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $response = $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'description' => 'Ross outbid Gunther by a dollar.',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'location' => 'New York',
        'from_party' => 'Gunther',
        'to_party' => 'Ross Geller',
        'reference_number' => 'Lot 118',
        'source_url' => 'https://example.com/lot-118',
        'is_verified' => '1',
        'verification_note' => 'Checked against the auction catalogue.',
    ]);

    $response->assertRedirect(route('items.history.section', [$collection, $item, $copy, 'provenance']));
    $response->assertSessionHas('status', 'Provenance event recorded');

    $event = ProvenanceEvent::query()->first();
    expect($event->copy_id)->toBe($copy->id);
    expect($event->type)->toBe(ProvenanceEventType::Acquisition);
    expect($event->title)->toBe('Bought at the Central Perk auction');
    expect($event->from_party)->toBe('Gunther');
    expect($event->to_party)->toBe('Ross Geller');
    expect($event->location)->toBe('New York');
    expect($event->reference_number)->toBe('Lot 118');
    expect($event->is_verified)->toBeTrue();
    expect($event->verification_note)->toBe('Checked against the auction catalogue.');
    expect($event->occurred_at->toDateString())->toBe('1987-06-02');
    expect($event->transaction_id)->toBeNull();
});

// The action refuses to store a date it was told is unknown, so nothing is left
// behind for a later edit to start reading as though it were known.
it('keeps no date when the precision is unknown', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Made in a factory nobody wrote down',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Unknown->value,
    ]);

    $event = ProvenanceEvent::query()->first();
    expect($event->occurred_at)->toBeNull();
    expect($event->occurred_at_precision)->toBe(DatePrecision::Unknown);
    expect($event->formattedDate())->toBe('Date unknown');
});

it('links a provenance event to a transaction of the same copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'transaction_id' => (string) $transaction->id,
    ]);

    expect(ProvenanceEvent::query()->first()->transaction_id)->toBe($transaction->id);
});

it('does not link a provenance event to a transaction of another copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $other = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $other->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'transaction_id' => (string) $transaction->id,
    ])->assertNotFound();

    expect(ProvenanceEvent::query()->count())->toBe(0);
});

it('leaves a provenance event unlinked when no transaction was chosen', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Gift->value,
        'title' => 'Given by Phoebe',
        'occurred_at' => '1998-11-02',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'transaction_id' => '',
    ]);

    expect(ProvenanceEvent::query()->first()->transaction_id)->toBeNull();
});

// A note about how something was verified means nothing when it was not.
it('drops the verification note when the event is not verified', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Authentication->value,
        'title' => 'Looked at by a specialist',
        'occurred_at' => '2001-04-01',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'is_verified' => '0',
        'verification_note' => 'Checked against the auction catalogue.',
    ]);

    $event = ProvenanceEvent::query()->first();
    expect($event->is_verified)->toBeFalse();
    expect($event->verification_note)->toBeNull();
});

it('validates the type, the title and the precision when recording a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [])
        ->assertSessionHasErrors(['type', 'title', 'occurred_at_precision']);
});

it('rejects an unknown provenance event type and an unknown precision', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => 'bartering-for-a-monkey',
        'title' => 'Marcel',
        'occurred_at_precision' => 'roughly-the-nineties',
    ])->assertSessionHasErrors(['type', 'occurred_at_precision']);
});

it('rejects a bad date and a bad source link on a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Sale->value,
        'title' => 'Sold to Gunther',
        'occurred_at' => 'the day Ross said we were on a break',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'source_url' => 'not a link',
    ])->assertSessionHasErrors(['occurred_at', 'source_url']);
});

it('forbids a viewer from recording a provenance event', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('does not record a provenance event against a copy of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$foreign, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('does not record a provenance event against a copy of another item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $other = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $other->id]);

    $this->actingAs($user)->post(route('provenanceEvents.create', [$collection, $item, $copy]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk auction',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('updates a provenance event', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'type' => ProvenanceEventType::Acquisition,
        'title' => 'Bought somewhere',
        'occurred_at' => '1987-06-02',
        'occurred_at_precision' => DatePrecision::Exact,
    ]);

    $response = $this->actingAs($user)->put(route('provenanceEvents.update', [$collection, $item, $copy, $event]), [
        'type' => ProvenanceEventType::Exhibition->value,
        'title' => 'Shown at the museum',
        'occurred_at' => '1994-01-01',
        'occurred_at_precision' => DatePrecision::Year->value,
        'to_party' => 'The Natural History Museum',
    ]);

    $response->assertRedirect(route('items.history.section', [$collection, $item, $copy, 'provenance']));
    $response->assertSessionHas('status', 'Provenance event updated');

    $event->refresh();
    expect($event->type)->toBe(ProvenanceEventType::Exhibition);
    expect($event->title)->toBe('Shown at the museum');
    expect($event->to_party)->toBe('The Natural History Museum');
    expect($event->occurred_at_precision)->toBe(DatePrecision::Year);
    expect($event->formattedDate())->toBe('1994');
});

it('unlinks a provenance event from its transaction when the link is cleared', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'transaction_id' => $transaction->id,
    ]);

    $this->actingAs($user)->put(route('provenanceEvents.update', [$collection, $item, $copy, $event]), [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought somewhere',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'transaction_id' => '',
    ]);

    expect($event->refresh()->transaction_id)->toBeNull();
});

it('validates the title when updating a provenance event', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->put(route('provenanceEvents.update', [$collection, $item, $copy, $event]), [])
        ->assertSessionHasErrors(['type', 'title', 'occurred_at_precision']);
});

it('forbids a viewer from updating a provenance event', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->put(route('provenanceEvents.update', [$collection, $item, $copy, $event]), [
        'type' => ProvenanceEventType::Sale->value,
        'title' => 'Sold to Gunther',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('does not update a provenance event of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->put(route('provenanceEvents.update', [$foreign, $item, $copy, $event]), [
        'type' => ProvenanceEventType::Sale->value,
        'title' => 'Sold to Gunther',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('does not update a provenance event that belongs to another copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $other = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $other->id]);

    $this->actingAs($user)->put(route('provenanceEvents.update', [$collection, $item, $copy, $event]), [
        'type' => ProvenanceEventType::Sale->value,
        'title' => 'Sold to Gunther',
        'occurred_at_precision' => DatePrecision::Exact->value,
    ])->assertNotFound();
});

it('deletes a provenance event', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $response = $this->actingAs($user)->delete(route('provenanceEvents.destroy', [$collection, $item, $copy, $event]));

    $response->assertRedirect(route('items.history.section', [$collection, $item, $copy, 'provenance']));
    $response->assertSessionHas('status', 'Provenance event deleted');
    $this->assertModelMissing($event);
});

it('forbids a viewer from deleting a provenance event', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($viewer)->delete(route('provenanceEvents.destroy', [$collection, $item, $copy, $event]))
        ->assertNotFound();

    $this->assertModelExists($event);
});

it('does not delete a provenance event of another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);
    $copy = Copy::factory()->create(['item_id' => $item->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    $this->actingAs($user)->delete(route('provenanceEvents.destroy', [$foreign, $item, $copy, $event]))
        ->assertNotFound();

    $this->assertModelExists($event);
});
