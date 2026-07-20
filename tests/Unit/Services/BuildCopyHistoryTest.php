<?php

declare(strict_types=1);

use App\Enums\DatePrecision;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Enums\MaintenanceType;
use App\Enums\TimelineSource;
use App\Enums\TransactionType;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Loan;
use App\Models\LocationHistory;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use App\Models\Valuation;
use App\Services\BuildCopyHistory;
use App\ValueObjects\TimelineEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * The source of every entry, in order, for asserting the merge and the sort.
 *
 * @param  list<TimelineEntry>  $entries
 * @return list<string>
 */
function sources(array $entries): array
{
    return array_map(fn (TimelineEntry $entry): string => $entry->source->value, $entries);
}

/**
 * The identity of every entry, in order.
 *
 * @param  list<TimelineEntry>  $entries
 * @return list<string>
 */
function keys(array $entries): array
{
    return array_map(fn (TimelineEntry $entry): string => $entry->key(), $entries);
}

it('merges an entry from every contributing source', function () {
    $copy = Copy::factory()->create();

    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '1987-01-01']);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '1990-01-01', 'occurred_at_precision' => DatePrecision::Year]);
    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'starts_at' => '2015-01-01']);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Restoration, 'performed_at' => '1998-01-01']);
    Loan::factory()->create(['copy_id' => $copy->id, 'include_in_provenance' => true, 'loaned_at' => '2020-01-01']);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2026-01-01']);

    $entries = new BuildCopyHistory($copy)->entries(meaningfulOnly: false);

    expect(sources($entries))->toContain(
        TimelineSource::Transaction->value,
        TimelineSource::Provenance->value,
        TimelineSource::Valuation->value,
        TimelineSource::Insurance->value,
        TimelineSource::Maintenance->value,
        TimelineSource::Loan->value,
        TimelineSource::Location->value,
    );
});

it('keeps only the meaningful entries by default', function () {
    $copy = Copy::factory()->create();

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Fee, 'occurred_at' => '2012-02-01']);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Cleaning, 'include_in_provenance' => false, 'performed_at' => '2012-03-01']);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2012-04-01']);

    $entries = new BuildCopyHistory($copy)->entries();

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->source)->toBe(TimelineSource::Valuation)
        ->and($entries[0]->sourceId)->toBe($valuation->id);
});

it('adds the routine entries in the complete view', function () {
    $copy = Copy::factory()->create();

    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Fee, 'occurred_at' => '2012-02-01']);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Cleaning, 'include_in_provenance' => false, 'performed_at' => '2012-03-01']);
    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2012-04-01']);

    $entries = new BuildCopyHistory($copy)->entries(meaningfulOnly: false);

    expect($entries)->toHaveCount(4);
});

it('treats a restoration as meaningful even without the flag', function () {
    $copy = Copy::factory()->create();
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Restoration, 'include_in_provenance' => false, 'performed_at' => '1998-01-01']);
    MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'type' => MaintenanceType::Cleaning, 'include_in_provenance' => false, 'performed_at' => '1999-01-01']);

    $entries = new BuildCopyHistory($copy)->entries();

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->source)->toBe(TimelineSource::Maintenance);
});

it('orders entries newest first across sources', function () {
    $copy = Copy::factory()->create();

    $old = ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '1987-01-01', 'occurred_at_precision' => DatePrecision::Year]);
    $mid = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    $new = InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'starts_at' => '2022-01-01']);

    $entries = new BuildCopyHistory($copy)->entries();

    expect(keys($entries))->toBe([
        TimelineSource::Insurance->value.'-'.$new->id,
        TimelineSource::Valuation->value.'-'.$mid->id,
        TimelineSource::Provenance->value.'-'.$old->id,
    ]);
});

it('drops undated and unknown entries to the end', function () {
    $copy = Copy::factory()->create();

    $dated = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    $undated = ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => null, 'occurred_at_precision' => DatePrecision::Unknown]);
    // A date sits in the column, but the precision says unknown, so it must not
    // start reading as dated.
    $unknownWithDate = ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '2050-01-01', 'occurred_at_precision' => DatePrecision::Unknown]);

    $entries = new BuildCopyHistory($copy)->entries();

    expect($entries[0]->source)->toBe(TimelineSource::Valuation)
        ->and($entries[0]->sourceId)->toBe($dated->id)
        ->and(collect($entries)->skip(1)->pluck('sourceId')->all())->toContain($undated->id, $unknownWithDate->id);
});

it('filters the timeline to the chosen sources', function () {
    $copy = Copy::factory()->create();

    $valuation = Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);
    ProvenanceEvent::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '1987-01-01', 'occurred_at_precision' => DatePrecision::Year]);

    $entries = new BuildCopyHistory($copy)->entries(types: [TimelineSource::Valuation->value]);

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->sourceId)->toBe($valuation->id);
});

it('produces a lending and a return entry for a returned loan', function () {
    $copy = Copy::factory()->create();

    $loan = Loan::factory()->create([
        'copy_id' => $copy->id,
        'direction' => LoanDirection::Outgoing,
        'status' => LoanStatus::Returned,
        'party' => 'The Louvre',
        'include_in_provenance' => true,
        'loaned_at' => '2025-01-01',
        'returned_at' => '2025-06-01',
    ]);

    $entries = new BuildCopyHistory($copy)->entries();

    expect(keys($entries))->toBe([
        TimelineSource::Loan->value.'-'.$loan->id.'-return',
        TimelineSource::Loan->value.'-'.$loan->id,
    ]);
});

it('does not produce a return entry for an open loan', function () {
    $copy = Copy::factory()->create();
    Loan::factory()->create(['copy_id' => $copy->id, 'include_in_provenance' => true, 'returned_at' => null]);

    $entries = new BuildCopyHistory($copy)->entries();

    expect($entries)->toHaveCount(1)
        ->and($entries[0]->qualifier)->toBeNull();
});

it('lists the present sources in enum order', function () {
    $copy = Copy::factory()->create();

    LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2026-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'valued_at' => '2012-01-01']);

    $present = new BuildCopyHistory($copy)->presentSources();

    // Valuation comes before Location in the enum, and insurance is absent, so it
    // is not offered.
    expect($present)->toBe([TimelineSource::Valuation, TimelineSource::Location]);
});
