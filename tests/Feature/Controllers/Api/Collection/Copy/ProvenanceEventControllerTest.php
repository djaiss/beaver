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
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create a copy belonging to the given account.
 */
function copyForProvenance(int $accountId): Copy
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    return Copy::factory()->create(['item_id' => $item->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'copy_id',
            'transaction_id',
            'type',
            'title',
            'description',
            'occurred_at',
            'occurred_at_precision',
            'formatted_date',
            'location',
            'from_party',
            'to_party',
            'reference_number',
            'source_url',
            'is_verified',
            'verification_note',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the provenance events of a copy', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/provenance-events')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list the provenance events of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($this->createAccount()->id);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/provenance-events')->assertNotFound();
});

it('shows a provenance event', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'type' => ProvenanceEventType::Acquisition,
        'title' => 'Bought at the Central Perk estate sale',
        'occurred_at' => '1987-06-15',
        'occurred_at_precision' => DatePrecision::Exact,
        'from_party' => 'Gunther',
        'to_party' => 'Ross Geller',
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'provenance_event')
        ->assertJsonPath('data.id', (string) $event->id)
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.transaction_id', null)
        ->assertJsonPath('data.attributes.type', 'acquisition')
        ->assertJsonPath('data.attributes.title', 'Bought at the Central Perk estate sale')
        ->assertJsonPath('data.attributes.from_party', 'Gunther')
        ->assertJsonPath('data.attributes.to_party', 'Ross Geller')
        ->assertJsonPath('data.links.self', route('api.copies.provenanceEvents.show', [$copy->id, $event->id]));
});

it('renders the date at the precision it was recorded at', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'occurred_at' => '1987-06-15',
        'occurred_at_precision' => DatePrecision::Year,
    ]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.occurred_at_precision', 'year')
        ->assertJsonPath('data.attributes.formatted_date', '1987');
});

it('does not show a provenance event of a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($this->createAccount()->id);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id)->assertNotFound();
});

it('creates a provenance event', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought at the Central Perk estate sale',
        'occurred_at' => '1987-06-15',
        'occurred_at_precision' => DatePrecision::Exact->value,
        'location' => 'New York',
        'from_party' => 'Gunther',
        'to_party' => 'Ross Geller',
        'is_verified' => true,
        'verification_note' => 'Certificate held on file.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.copy_id', (string) $copy->id)
        ->assertJsonPath('data.attributes.type', 'acquisition')
        ->assertJsonPath('data.attributes.title', 'Bought at the Central Perk estate sale')
        ->assertJsonPath('data.attributes.location', 'New York')
        ->assertJsonPath('data.attributes.is_verified', true);

    $event = ProvenanceEvent::query()->latest('id')->first();
    expect($event->copy_id)->toBe($copy->id);
    expect($event->type)->toBe(ProvenanceEventType::Acquisition);
    expect($event->occurred_at->toDateString())->toBe('1987-06-15');
    expect($event->verification_note)->toBe('Certificate held on file.');
});

it('stores no date when the precision is unknown', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Made somewhere, at some point',
        'occurred_at' => '1987-06-15',
        'occurred_at_precision' => DatePrecision::Unknown->value,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.occurred_at', null)
        ->assertJsonPath('data.attributes.occurred_at_precision', 'unknown')
        ->assertJsonPath('data.attributes.formatted_date', 'Date unknown');

    $event = ProvenanceEvent::query()->latest('id')->first();
    expect($event->occurred_at)->toBeNull();
});

it('links a provenance event to a transaction of the same copy', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther',
        'transaction_id' => $transaction->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.attributes.transaction_id', (string) $transaction->id);
});

it('refuses to link a provenance event to a transaction of another copy', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $otherCopy = copyForProvenance($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $otherCopy->id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther',
        'transaction_id' => $transaction->id,
    ])->assertNotFound();
});

it('refuses to link a provenance event to a transaction that already has one', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'transaction_id' => $transaction->id,
    ]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther, again',
        'transaction_id' => $transaction->id,
    ])->assertNotFound();
});

it('validates the type when creating a provenance event', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => 'smelly-cat',
        'title' => 'Smelly cat',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('validates the precision when creating a provenance event', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Smelly cat',
        'occurred_at_precision' => 'pivot',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['occurred_at_precision']);
});

it('requires a type and a title when creating a provenance event', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type', 'title']);
});

it('does not create a provenance event on a copy from another account', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($this->createAccount()->id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Printed in New York',
    ])->assertNotFound();
});

it('restricts provenance event creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForProvenance($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/copies/'.$copy->id.'/provenance-events', [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Printed in New York',
    ])->assertNotFound();
});

it('updates a provenance event', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'type' => ProvenanceEventType::Acquisition,
        'title' => 'Bought from Gunther',
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id, [
        'type' => ProvenanceEventType::Exhibition->value,
        'title' => 'Shown at the Museum of Natural History',
        'occurred_at' => '1994-09-22',
        'occurred_at_precision' => DatePrecision::Month->value,
        'location' => 'New York',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.type', 'exhibition')
        ->assertJsonPath('data.attributes.title', 'Shown at the Museum of Natural History')
        ->assertJsonPath('data.attributes.occurred_at_precision', 'month')
        ->assertJsonPath('data.attributes.formatted_date', 'September 1994');

    $event->refresh();
    expect($event->type)->toBe(ProvenanceEventType::Exhibition);
    expect($event->occurred_at->toDateString())->toBe('1994-09-22');
});

it('allows relinking a provenance event to the transaction it already carries', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    $event = ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'transaction_id' => $transaction->id,
    ]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id, [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther',
        'transaction_id' => $transaction->id,
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.transaction_id', (string) $transaction->id);
});

it('refuses to update a provenance event with a transaction of another copy', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $otherCopy = copyForProvenance($user->account_id);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);
    $transaction = Transaction::factory()->create(['copy_id' => $otherCopy->id]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id, [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther',
        'transaction_id' => $transaction->id,
    ])->assertNotFound();
});

it('refuses to update a provenance event with a transaction another event already has', function () {
    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    ProvenanceEvent::factory()->create([
        'copy_id' => $copy->id,
        'transaction_id' => $transaction->id,
    ]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id, [
        'type' => ProvenanceEventType::Acquisition->value,
        'title' => 'Bought from Gunther',
        'transaction_id' => $transaction->id,
    ])->assertNotFound();
});

it('restricts provenance event updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForProvenance($account->id);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id, [
        'type' => ProvenanceEventType::Origin->value,
        'title' => 'Printed in New York',
    ])->assertNotFound();
});

it('deletes a provenance event', function () {
    Queue::fake();

    $user = $this->createUser();
    $copy = copyForProvenance($user->account_id);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id)->assertNoContent();

    $this->assertModelMissing($event);
});

it('restricts provenance event deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $copy = copyForProvenance($account->id);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/copies/'.$copy->id.'/provenance-events/'.$event->id)->assertNotFound();
});
