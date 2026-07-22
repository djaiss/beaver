<?php

declare(strict_types=1);
use App\Enums\CopyStatus;
use App\Enums\TransactionType;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ItemCondition;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\Valuation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to an item', function () {
    $item = Item::factory()->create();
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    expect($copy->item)->toBeInstanceOf(Item::class);
    expect($copy->item->id)->toBe($item->id);
});

it('belongs to a condition', function () {
    $condition = ItemCondition::factory()->create();
    $copy = Copy::factory()->create(['item_condition_id' => $condition->id]);

    expect($copy->itemCondition)->toBeInstanceOf(ItemCondition::class);
    expect($copy->itemCondition->id)->toBe($condition->id);
});

it('has no condition when unknown', function () {
    $copy = Copy::factory()->create(['item_condition_id' => null]);

    expect($copy->itemCondition)->toBeNull();
});

it('belongs to a current location', function () {
    $location = Location::factory()->create();
    $copy = Copy::factory()->create(['current_location_id' => $location->id]);

    expect($copy->currentLocation)->toBeInstanceOf(Location::class);
    expect($copy->currentLocation->id)->toBe($location->id);
});

it('has no current location when unknown', function () {
    $copy = Copy::factory()->create(['current_location_id' => null]);

    expect($copy->currentLocation)->toBeNull();
});

it('casts the status to an enum and defaults to owned', function () {
    $copy = Copy::factory()->create();

    expect($copy->status)->toBe(CopyStatus::Owned);

    $sold = Copy::factory()->create(['status' => CopyStatus::Sold]);

    expect($sold->status)->toBe(CopyStatus::Sold);
});

it('defaults to a quantity of one', function () {
    $copy = Copy::factory()->create();

    expect($copy->quantity)->toBe(1);
});

it('casts the disposal date', function () {
    $copy = Copy::factory()->create(['disposed_at' => '2026-07-17']);

    expect($copy->disposed_at->toDateString())->toBe('2026-07-17');
});

it('encrypts the identifier and the note', function () {
    $copy = Copy::factory()->create([
        'identifier' => 'ASM1-A',
        'note' => 'Bought at the Central Perk fair.',
    ]);

    $raw = fn (string $column): string => DB::table('copies')->where('id', $copy->id)->value($column);

    $this->assertNotSame('ASM1-A', $raw('identifier'));
    expect(decrypt($raw('identifier'), false))->toBe('ASM1-A');
    expect(decrypt($raw('note'), false))->toBe('Bought at the Central Perk fair.');
});

it('has many valuations, most recent first', function () {
    $copy = Copy::factory()->create();
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 1000, 'valued_at' => '2024-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 3000, 'valued_at' => '2026-01-01']);

    expect($copy->valuations)->toHaveCount(2);
    expect($copy->valuations->first()->amount)->toBe(3000);
});

it('reads its estimated value from the latest valuation', function () {
    $copy = Copy::factory()->create();
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 1000, 'valued_at' => '2024-01-01']);
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 3000, 'valued_at' => '2026-01-01']);

    expect($copy->estimatedValue())->toBe(3000);
});

// Two valuations on the same day would otherwise be picked between arbitrarily,
// so the later row has to win.
it('breaks a tie on the valuation date with the newer row', function () {
    $copy = Copy::factory()->create();
    Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 1000, 'valued_at' => '2026-01-01']);
    $newer = Valuation::factory()->create(['copy_id' => $copy->id, 'amount' => 2000, 'valued_at' => '2026-01-01']);

    expect($copy->estimatedValue())->toBe(2000);
    expect($copy->latestValuation->id)->toBe($newer->id);
});

// Never valued is not the same as worth nothing, so this must not read as zero.
it('has no estimated value until it has been valued', function () {
    $copy = Copy::factory()->create();

    expect($copy->estimatedValue())->toBeNull();
});

it('soft deletes', function () {
    $copy = Copy::factory()->create();

    $copy->delete();

    $this->assertSoftDeleted($copy);
    expect(Copy::query()->find($copy->id))->toBeNull();
    expect(Copy::withTrashed()->find($copy->id))->not->toBeNull();
});

it('has many transactions, most recent first', function () {
    $copy = Copy::factory()->create();
    Transaction::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '2024-01-01']);
    Transaction::factory()->create(['copy_id' => $copy->id, 'occurred_at' => '2026-01-01']);

    expect($copy->transactions)->toHaveCount(2);
    expect($copy->transactions->first()->occurred_at->toDateString())->toBe('2026-01-01');
});

// The acquisition is the earliest transaction that brought the copy in, so a
// copy bought and later sold still reports when it was bought.
it('reads its acquisition from the earliest acquiring transaction', function () {
    $copy = Copy::factory()->create();
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Sale, 'occurred_at' => '2020-01-01']);
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '2024-06-01', 'amount' => 1000, 'fee_amount' => 200, 'shipping_amount' => 300, 'total_amount' => null]);
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Purchase, 'occurred_at' => '2026-01-01']);

    expect($copy->acquiredAt()->toDateString())->toBe('2024-06-01');
    expect($copy->pricePaid())->toBe(1500);
});

// A fee is money around an acquisition, not the acquisition itself.
it('does not read its acquisition from a fee', function () {
    $copy = Copy::factory()->create();
    Transaction::factory()->create(['copy_id' => $copy->id, 'type' => TransactionType::Fee, 'occurred_at' => '2020-01-01']);

    expect($copy->acquiredAt())->toBeNull();
    expect($copy->pricePaid())->toBeNull();
});

it('has no acquisition until a transaction says how it was acquired', function () {
    $copy = Copy::factory()->create();

    expect($copy->acquiredAt())->toBeNull();
    expect($copy->pricePaid())->toBeNull();
});
