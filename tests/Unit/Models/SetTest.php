<?php

declare(strict_types=1);
use App\Models\Account;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('reaches its account through its collection', function () {
    $account = Account::factory()->create();
    $set = Set::factory()->forAccount($account->id)->create();

    expect($set->collection->account)->toBeInstanceOf(Account::class);
    expect($set->collection->account->id)->toBe($account->id);
    expect($account->sets->pluck('id'))->toContain($set->id);
});

it('belongs to a collection', function () {
    $collection = Collection::factory()->create();
    $set = Set::factory()->create(['collection_id' => $collection->id]);

    expect($set->collection)->toBeInstanceOf(Collection::class);
    expect($set->collection->id)->toBe($collection->id);
});

it('stores the target count as an integer', function () {
    $set = Set::factory()->create(['target_count' => 10]);

    expect($set->refresh()->target_count)->toBe(10);
});

it('allows a set without a target count', function () {
    $set = Set::factory()->create(['target_count' => null]);

    expect($set->refresh()->target_count)->toBeNull();
});

it('has many items', function () {
    $set = Set::factory()->create();
    $item = Item::factory()->create(['set_id' => $set->id]);

    expect($set->items->pluck('id'))->toContain($item->id);
});

it('encrypts the name and description at rest', function () {
    $set = Set::factory()->create(['name' => 'Amazing Spider-Man #1-10', 'description' => 'The Lee run.']);

    $rawName = DB::table('sets')->where('id', $set->id)->value('name');
    $rawDescription = DB::table('sets')->where('id', $set->id)->value('description');

    $this->assertNotSame('Amazing Spider-Man #1-10', $rawName);
    expect(decrypt($rawName, false))->toBe('Amazing Spider-Man #1-10');
    expect(decrypt($rawDescription, false))->toBe('The Lee run.');
});

it('soft deletes', function () {
    $set = Set::factory()->create();

    $set->delete();

    $this->assertSoftDeleted($set);
});
