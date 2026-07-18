<?php

declare(strict_types=1);
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('belongs to an item', function () {
    $item = Item::factory()->create();
    $copy = Copy::factory()->create(['item_id' => $item->id]);

    expect($copy->item)->toBeInstanceOf(Item::class);
    expect($copy->item->id)->toBe($item->id);
});

it('belongs to a condition', function () {
    $condition = Condition::factory()->create();
    $copy = Copy::factory()->create(['condition_id' => $condition->id]);

    expect($copy->condition)->toBeInstanceOf(Condition::class);
    expect($copy->condition->id)->toBe($condition->id);
});

it('has no condition when unknown', function () {
    $copy = Copy::factory()->create(['condition_id' => null]);

    expect($copy->condition)->toBeNull();
});

it('belongs to a location', function () {
    $location = Location::factory()->create();
    $copy = Copy::factory()->create(['location_id' => $location->id]);

    expect($copy->location)->toBeInstanceOf(Location::class);
    expect($copy->location->id)->toBe($location->id);
});

it('has no location when unknown', function () {
    $copy = Copy::factory()->create(['location_id' => null]);

    expect($copy->location)->toBeNull();
});

it('casts the money fields to integers', function () {
    $copy = Copy::factory()->create([
        'price_paid' => 4200,
        'estimated_value' => 9900,
    ]);

    expect($copy->price_paid)->toBe(4200);
    expect($copy->estimated_value)->toBe(9900);
});

it('casts the acquired date', function () {
    $copy = Copy::factory()->create(['acquired_at' => '2026-07-17']);

    expect($copy->acquired_at->toDateString())->toBe('2026-07-17');
});

it('soft deletes', function () {
    $copy = Copy::factory()->create();

    $copy->delete();

    $this->assertSoftDeleted($copy);
    expect(Copy::query()->find($copy->id))->toBeNull();
    expect(Copy::withTrashed()->find($copy->id))->not->toBeNull();
});
