<?php

declare(strict_types=1);
use App\Enums\InsuranceStatus;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $record = InsuranceRecord::factory()->create(['copy_id' => $copy->id]);

    expect($record->copy)->toBeInstanceOf(Copy::class);
    expect($record->copy->id)->toBe($copy->id);
});

it('casts the status to an enum and the amounts and dates', function () {
    $record = InsuranceRecord::factory()->create([
        'status' => InsuranceStatus::Expired,
        'insured_value' => 45000,
        'deductible_amount' => 5000,
        'starts_at' => '2024-01-01',
        'ends_at' => '2025-01-01',
        'is_scheduled_item' => true,
    ]);

    expect($record->status)->toBe(InsuranceStatus::Expired);
    expect($record->insured_value)->toBe(45000);
    expect($record->deductible_amount)->toBe(5000);
    expect($record->starts_at->toDateString())->toBe('2024-01-01');
    expect($record->ends_at->toDateString())->toBe('2025-01-01');
    expect($record->is_scheduled_item)->toBeTrue();
});

it('encrypts the free text and contact fields', function () {
    $record = InsuranceRecord::factory()->create([
        'provider' => 'Collectibles Insurance Services',
        'policy_number' => 'CIS-88231',
        'coverage_type' => 'Scheduled item',
        'contact_name' => 'Dana Whitfield',
        'contact_email' => 'dana@cisinsurance.com',
        'contact_phone' => '+1 888 837 9537',
        'note' => 'On the fine-collectibles rider.',
    ]);

    $raw = fn (string $column): string => DB::table('insurance_records')->where('id', $record->id)->value($column);

    $this->assertNotSame('Collectibles Insurance Services', $raw('provider'));
    expect(decrypt($raw('provider'), false))->toBe('Collectibles Insurance Services');
    expect(decrypt($raw('policy_number'), false))->toBe('CIS-88231');
    expect(decrypt($raw('coverage_type'), false))->toBe('Scheduled item');
    expect(decrypt($raw('contact_name'), false))->toBe('Dana Whitfield');
    expect(decrypt($raw('contact_email'), false))->toBe('dana@cisinsurance.com');
    expect(decrypt($raw('contact_phone'), false))->toBe('+1 888 837 9537');
    expect(decrypt($raw('note'), false))->toBe('On the fine-collectibles rider.');
});

it('reads the active record for a copy through the helper', function () {
    $copy = Copy::factory()->create();
    InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'status' => InsuranceStatus::Expired, 'starts_at' => '2023-01-01']);
    $active = InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'status' => InsuranceStatus::Active, 'starts_at' => '2024-01-01']);

    expect($copy->activeInsuranceRecord->id)->toBe($active->id);
});

it('has no active record when none is in force', function () {
    $copy = Copy::factory()->create();
    InsuranceRecord::factory()->create(['copy_id' => $copy->id, 'status' => InsuranceStatus::Cancelled]);

    expect($copy->activeInsuranceRecord)->toBeNull();
});
