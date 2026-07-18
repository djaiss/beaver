<?php

declare(strict_types=1);
use App\Models\Category;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a collection', function () {
    $collection = Collection::factory()->create();
    $category = Category::factory()->create(['collection_id' => $collection->id]);

    expect($category->collection)->toBeInstanceOf(Collection::class);
    expect($category->collection->id)->toBe($collection->id);
});

it('nests through a parent and children', function () {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id, 'collection_id' => $parent->collection_id]);

    expect($child->parent->id)->toBe($parent->id);
    expect($parent->children->pluck('id'))->toContain($child->id);
});

it('has many items', function () {
    $category = Category::factory()->create();
    $item = Item::factory()->create(['category_id' => $category->id, 'collection_id' => $category->collection_id]);

    expect($category->items->pluck('id'))->toContain($item->id);
});

it('encrypts the name at rest', function () {
    $category = Category::factory()->create(['name' => 'Spider-Man']);

    $raw = DB::table('categories')->where('id', $category->id)->value('name');

    $this->assertNotSame('Spider-Man', $raw);
    expect(decrypt($raw, false))->toBe('Spider-Man');
});

it('soft deletes', function () {
    $category = Category::factory()->create();

    $category->delete();

    $this->assertSoftDeleted($category);
});
