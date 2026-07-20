<?php

declare(strict_types=1);
use App\Models\Copy;
use App\Models\Location;
use App\Models\LocationHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy and a location', function () {
    $copy = Copy::factory()->create();
    $location = Location::factory()->create();
    $record = LocationHistory::factory()->create(['copy_id' => $copy->id, 'location_id' => $location->id]);

    expect($record->copy)->toBeInstanceOf(Copy::class);
    expect($record->copy->id)->toBe($copy->id);
    expect($record->location)->toBeInstanceOf(Location::class);
    expect($record->location->id)->toBe($location->id);
});

it('casts the dates', function () {
    $record = LocationHistory::factory()->create([
        'moved_at' => '2024-01-01',
        'moved_out_at' => '2024-06-01',
    ]);

    expect($record->moved_at->toDateString())->toBe('2024-01-01');
    expect($record->moved_out_at->toDateString())->toBe('2024-06-01');
});

it('encrypts the reason and the note', function () {
    $record = LocationHistory::factory()->create([
        'reason' => 'Rotated into the display case',
        'note' => 'Front row, eye level.',
    ]);

    $raw = fn (string $column): string => DB::table('location_history')->where('id', $record->id)->value($column);

    $this->assertNotSame('Rotated into the display case', $raw('reason'));
    expect(decrypt($raw('reason'), false))->toBe('Rotated into the display case');
    expect(decrypt($raw('note'), false))->toBe('Front row, eye level.');
});

it('reads a record with no end as open', function () {
    $open = LocationHistory::factory()->create(['moved_out_at' => null]);
    $closed = LocationHistory::factory()->closed()->create();

    expect($open->isOpen())->toBeTrue();
    expect($closed->isOpen())->toBeFalse();
});

it('reads the single open record for a copy through the helper', function () {
    $copy = Copy::factory()->create();
    LocationHistory::factory()->closed()->create(['copy_id' => $copy->id, 'moved_at' => '2023-01-01']);
    $open = LocationHistory::factory()->create(['copy_id' => $copy->id, 'moved_at' => '2024-01-01', 'moved_out_at' => null]);

    expect($copy->openLocationHistory->id)->toBe($open->id);
});

it('has no open record when the copy has moved out of everywhere', function () {
    $copy = Copy::factory()->create();
    LocationHistory::factory()->closed()->create(['copy_id' => $copy->id]);

    expect($copy->openLocationHistory)->toBeNull();
});
