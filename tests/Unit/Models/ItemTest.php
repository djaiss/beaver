<?php

declare(strict_types=1);
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a collection', function () {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    expect($item->collection)->toBeInstanceOf(Collection::class);
    expect($item->collection->id)->toBe($collection->id);
});

it('belongs to a collection type', function () {
    $collectionType = CollectionType::factory()->create();
    $item = Item::factory()->create(['type_id' => $collectionType->id]);

    expect($item->collectionType)->toBeInstanceOf(CollectionType::class);
    expect($item->collectionType->id)->toBe($collectionType->id);
});

it('has no collection type when untyped', function () {
    $item = Item::factory()->create(['type_id' => null]);

    expect($item->collectionType)->toBeNull();
});

it('encrypts the name at rest', function () {
    $item = Item::factory()->create(['name' => 'Amazing Spider-Man #1']);

    $rawName = DB::table('items')->where('id', $item->id)->value('name');

    $this->assertNotSame('Amazing Spider-Man #1', $rawName);
    expect(decrypt($rawName, false))->toBe('Amazing Spider-Man #1');
    expect($item->fresh()->name)->toBe('Amazing Spider-Man #1');
});

it('encrypts the description at rest', function () {
    $item = Item::factory()->create(['description' => 'The one where Joey buys a comic.']);

    $rawDescription = DB::table('items')->where('id', $item->id)->value('description');

    $this->assertNotSame('The one where Joey buys a comic.', $rawDescription);
    expect(decrypt($rawDescription, false))->toBe('The one where Joey buys a comic.');
});

it('soft deletes', function () {
    $item = Item::factory()->create();

    $item->delete();

    $this->assertSoftDeleted($item);
    expect(Item::query()->find($item->id))->toBeNull();
    expect(Item::withTrashed()->find($item->id))->not->toBeNull();
});
