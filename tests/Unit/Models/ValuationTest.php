<?php

declare(strict_types=1);
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Models\Copy;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $valuation = Valuation::factory()->create(['copy_id' => $copy->id]);

    expect($valuation->copy)->toBeInstanceOf(Copy::class);
    expect($valuation->copy->id)->toBe($copy->id);
});

it('casts the type and the confidence to enums', function () {
    $valuation = Valuation::factory()->create([
        'type' => ValuationType::ProfessionalAppraisal,
        'confidence' => ValuationConfidence::High,
    ]);

    expect($valuation->type)->toBe(ValuationType::ProfessionalAppraisal);
    expect($valuation->confidence)->toBe(ValuationConfidence::High);
});

it('casts the amount to an integer and the valuation date', function () {
    $valuation = Valuation::factory()->create([
        'amount' => 45000,
        'valued_at' => '2026-03-14',
    ]);

    expect($valuation->amount)->toBe(45000);
    expect($valuation->valued_at->toDateString())->toBe('2026-03-14');
});

it('encrypts the free text fields', function () {
    $valuation = Valuation::factory()->create([
        'valuer' => 'Rachel Green',
        'method' => 'Comparable sales',
        'source_url' => 'https://example.com/lot/42',
        'reference_number' => 'CP-1994',
        'note' => 'Valued after the Central Perk auction.',
    ]);

    $raw = fn (string $column): string => DB::table('valuations')->where('id', $valuation->id)->value($column);

    $this->assertNotSame('Rachel Green', $raw('valuer'));
    expect(decrypt($raw('valuer'), false))->toBe('Rachel Green');
    expect(decrypt($raw('method'), false))->toBe('Comparable sales');
    expect(decrypt($raw('source_url'), false))->toBe('https://example.com/lot/42');
    expect(decrypt($raw('reference_number'), false))->toBe('CP-1994');
    expect(decrypt($raw('note'), false))->toBe('Valued after the Central Perk auction.');
});
