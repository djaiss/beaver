<?php

declare(strict_types=1);
use App\Enums\TransactionType;
use App\Models\Copy;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a copy', function () {
    $copy = Copy::factory()->create();
    $transaction = Transaction::factory()->create(['copy_id' => $copy->id]);

    expect($transaction->copy)->toBeInstanceOf(Copy::class);
    expect($transaction->copy->id)->toBe($copy->id);
});

it('casts the type to an enum', function () {
    $transaction = Transaction::factory()->create(['type' => TransactionType::Inheritance]);

    expect($transaction->type)->toBe(TransactionType::Inheritance);
});

it('casts the money fields to integers and the date', function () {
    $transaction = Transaction::factory()->create([
        'amount' => 18000,
        'tax_amount' => 0,
        'fee_amount' => 2700,
        'shipping_amount' => 1800,
        'total_amount' => 22500,
        'occurred_at' => '2026-01-08',
    ]);

    expect($transaction->amount)->toBe(18000);
    expect($transaction->fee_amount)->toBe(2700);
    expect($transaction->total_amount)->toBe(22500);
    expect($transaction->occurred_at->toDateString())->toBe('2026-01-08');
});

it('encrypts the free text fields', function () {
    $transaction = Transaction::factory()->create([
        'counterparty' => 'Central Perk Auctions',
        'reference_number' => 'Lot #4021',
        'source_url' => 'https://example.com/lot/4021',
        'note' => 'Won at the January sale.',
    ]);

    $raw = fn (string $column): string => DB::table('transactions')->where('id', $transaction->id)->value($column);

    $this->assertNotSame('Central Perk Auctions', $raw('counterparty'));
    expect(decrypt($raw('counterparty'), false))->toBe('Central Perk Auctions');
    expect(decrypt($raw('reference_number'), false))->toBe('Lot #4021');
    expect(decrypt($raw('source_url'), false))->toBe('https://example.com/lot/4021');
    expect(decrypt($raw('note'), false))->toBe('Won at the January sale.');
});

it('reads the total that was recorded', function () {
    $transaction = Transaction::factory()->create([
        'amount' => 18000,
        'total_amount' => 22500,
    ]);

    expect($transaction->total())->toBe(22500);
});

// Someone recording a purchase in a hurry gives the price and nothing else, so
// the total has to fall out of the parts rather than come back empty.
it('falls back to the sum of the parts when no total was recorded', function () {
    $transaction = Transaction::factory()->create([
        'amount' => 18000,
        'tax_amount' => 500,
        'fee_amount' => 2700,
        'shipping_amount' => 1800,
        'total_amount' => null,
    ]);

    expect($transaction->total())->toBe(23000);
});

// A recorded total wins even when it disagrees with the parts: it is what
// actually changed hands, and the parts may be an incomplete breakdown of it.
it('prefers the recorded total over the sum of the parts', function () {
    $transaction = Transaction::factory()->create([
        'amount' => 100,
        'fee_amount' => 100,
        'total_amount' => 150,
    ]);

    expect($transaction->total())->toBe(150);
});

// Nothing recorded is not the same as a total of zero.
it('has no total when no amount was recorded at all', function () {
    $transaction = Transaction::factory()->create([
        'amount' => null,
        'tax_amount' => null,
        'fee_amount' => null,
        'shipping_amount' => null,
        'total_amount' => null,
    ]);

    expect($transaction->total())->toBeNull();
});

it('goes with the copy it belongs to', function () {
    $copy = Copy::factory()->create();
    Transaction::factory()->create(['copy_id' => $copy->id]);

    expect($copy->transactions()->exists())->toBeTrue();
});
