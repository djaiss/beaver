<?php

declare(strict_types=1);
use App\Enums\DatePrecision;
use App\Enums\ProvenanceEventType;
use App\Models\Copy;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id]);

    expect($event->copy)->toBeInstanceOf(Copy::class);
    expect($event->copy->id)->toBe($copy->id);
});

it('belongs to a transaction when it came from one', function () {
    $copy = Copy::factory()->create();
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);
    $event = ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'transaction_id' => $transaction->id]);

    expect($event->transaction)->toBeInstanceOf(Transaction::class);
    expect($transaction->provenanceEvent->id)->toBe($event->id);
});

it('has no transaction when it came from none', function () {
    $event = ProvenanceEvent::factory()->create(['transaction_id' => null]);

    expect($event->transaction)->toBeNull();
});

it('casts the type and the precision to enums', function () {
    $event = ProvenanceEvent::factory()->create([
        'type' => ProvenanceEventType::Authentication,
        'occurred_at_precision' => DatePrecision::Year,
    ]);

    expect($event->type)->toBe(ProvenanceEventType::Authentication);
    expect($event->occurred_at_precision)->toBe(DatePrecision::Year);
});

it('casts the verified flag to a boolean and defaults to false', function () {
    expect(ProvenanceEvent::factory()->create()->is_verified)->toBeFalse();
    expect(ProvenanceEvent::factory()->create(['is_verified' => true])->is_verified)->toBeTrue();
});

it('encrypts the free text fields', function () {
    $event = ProvenanceEvent::factory()->create([
        'title' => 'Acquired at the Central Perk sale',
        'description' => 'Bought on the floor, unslabbed.',
        'location' => 'New York',
        'from_party' => 'Gunther',
        'to_party' => 'Ross Geller',
        'reference_number' => 'Lot #4021',
        'source_url' => 'https://example.com/lot/4021',
        'verification_note' => 'Certificate held on file.',
    ]);

    $raw = fn (string $column): string => DB::table('provenance_events')->where('id', $event->id)->value($column);

    $this->assertNotSame('Acquired at the Central Perk sale', $raw('title'));
    expect(decrypt($raw('title'), false))->toBe('Acquired at the Central Perk sale');
    expect(decrypt($raw('description'), false))->toBe('Bought on the floor, unslabbed.');
    expect(decrypt($raw('location'), false))->toBe('New York');
    expect(decrypt($raw('from_party'), false))->toBe('Gunther');
    expect(decrypt($raw('to_party'), false))->toBe('Ross Geller');
    expect(decrypt($raw('reference_number'), false))->toBe('Lot #4021');
    expect(decrypt($raw('source_url'), false))->toBe('https://example.com/lot/4021');
    expect(decrypt($raw('verification_note'), false))->toBe('Certificate held on file.');
});

it('renders its date at the precision it was recorded at', function () {
    $event = ProvenanceEvent::factory()->create([
        'occurred_at' => '1987-03-12',
        'occurred_at_precision' => DatePrecision::Year,
    ]);

    expect($event->formattedDate())->toBe('1987');
    expect($event->shortDate())->toBe('1987');
});

// Provenance reads as a narrative, so the events run forwards rather than
// newest first the way the transactions do.
it('reads the events of a copy oldest first', function () {
    $copy = Copy::factory()->create();
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '2005-01-01', 'title' => 'Later']);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '1987-01-01', 'title' => 'Earlier']);

    expect($copy->provenanceEvents->pluck('title')->all())->toBe(['Earlier', 'Later']);
});

// The unique index on transaction_id has to exempt null, or a copy could only
// ever have one event that came from no transaction.
it('lets any number of events carry no transaction', function () {
    $copy = Copy::factory()->create();
    ProvenanceEvent::factory()->count(3)->create(['copy_id' => $copy->id, 'transaction_id' => null]);

    expect($copy->provenanceEvents)->toHaveCount(3);
});
