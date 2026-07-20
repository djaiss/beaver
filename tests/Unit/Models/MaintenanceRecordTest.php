<?php

declare(strict_types=1);
use App\Enums\MaintenanceType;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\MaintenanceRecord;
use App\Models\ProvenanceEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $record = MaintenanceRecord::factory()->create(['copy_id' => $copy->id]);

    expect($record->copy)->toBeInstanceOf(Copy::class);
    expect($record->copy->id)->toBe($copy->id);
});

it('belongs to a condition before and after', function () {
    $before = Condition::factory()->create();
    $after = Condition::factory()->create();
    $record = MaintenanceRecord::factory()->create([
        'condition_before_id' => $before->id,
        'condition_after_id' => $after->id,
    ]);

    expect($record->conditionBefore)->toBeInstanceOf(Condition::class);
    expect($record->conditionBefore->id)->toBe($before->id);
    expect($record->conditionAfter->id)->toBe($after->id);
});

it('belongs to the provenance event it generated', function () {
    $event = ProvenanceEvent::factory()->create();
    $record = MaintenanceRecord::factory()->create(['provenance_event_id' => $event->id]);

    expect($record->provenanceEvent)->toBeInstanceOf(ProvenanceEvent::class);
    expect($record->provenanceEvent->id)->toBe($event->id);
});

it('casts the type to an enum, the cost, the dates and the flag', function () {
    $record = MaintenanceRecord::factory()->create([
        'type' => MaintenanceType::Restoration,
        'cost_amount' => 12000,
        'performed_at' => '2024-01-01',
        'next_due_at' => '2025-01-01',
        'include_in_provenance' => true,
    ]);

    expect($record->type)->toBe(MaintenanceType::Restoration);
    expect($record->cost_amount)->toBe(12000);
    expect($record->performed_at->toDateString())->toBe('2024-01-01');
    expect($record->next_due_at->toDateString())->toBe('2025-01-01');
    expect($record->include_in_provenance)->toBeTrue();
});

it('encrypts the free text fields', function () {
    $record = MaintenanceRecord::factory()->create([
        'title' => 'Archival cleaning and re-housing',
        'description' => 'Surface cleaned and re-boxed in archival storage.',
        'performed_by' => 'Atelier Restauration',
    ]);

    $raw = fn (string $column): string => DB::table('maintenance_records')->where('id', $record->id)->value($column);

    $this->assertNotSame('Archival cleaning and re-housing', $raw('title'));
    expect(decrypt($raw('title'), false))->toBe('Archival cleaning and re-housing');
    expect(decrypt($raw('description'), false))->toBe('Surface cleaned and re-boxed in archival storage.');
    expect(decrypt($raw('performed_by'), false))->toBe('Atelier Restauration');
});

it('reads a record as due soon when its next date is near or past', function () {
    $overdue = MaintenanceRecord::factory()->create(['next_due_at' => now()->subDay()->toDateString()]);
    $soon = MaintenanceRecord::factory()->create(['next_due_at' => now()->addDays(10)->toDateString()]);

    expect($overdue->isDueSoon())->toBeTrue();
    expect($soon->isDueSoon())->toBeTrue();
});

it('does not read a record as due soon when its next date is far off or absent', function () {
    $far = MaintenanceRecord::factory()->create(['next_due_at' => now()->addYear()->toDateString()]);
    $none = MaintenanceRecord::factory()->create(['next_due_at' => null]);

    expect($far->isDueSoon())->toBeFalse();
    expect($none->isDueSoon())->toBeFalse();
});

it('reads its maintenance records off a copy, most recent first', function () {
    $copy = Copy::factory()->create();
    $older = MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'performed_at' => '2023-01-01']);
    $newer = MaintenanceRecord::factory()->create(['copy_id' => $copy->id, 'performed_at' => '2024-01-01']);

    expect($copy->maintenanceRecords->pluck('id')->all())->toBe([$newer->id, $older->id]);
});
