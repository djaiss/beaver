<?php

declare(strict_types=1);
use App\Models\Catalog;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a collection', function () {
    $catalog = Catalog::factory()->create();
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    expect($category->catalog)->toBeInstanceOf(Catalog::class);
    expect($category->catalog->id)->toBe($catalog->id);
});

it('nests through a parent and children', function () {
    $parent = Category::factory()->create();
    $child = Category::factory()->create(['parent_id' => $parent->id, 'catalog_id' => $parent->catalog_id]);

    expect($child->parent->id)->toBe($parent->id);
    expect($parent->children->pluck('id'))->toContain($child->id);
});

it('has many items', function () {
    $category = Category::factory()->create();
    $item = Item::factory()->create(['category_id' => $category->id, 'catalog_id' => $category->catalog_id]);

    expect($category->items->pluck('id'))->toContain($item->id);
});

it('encrypts the name at rest', function () {
    $category = Category::factory()->create(['name' => 'Spider-Man']);

    $raw = DB::table('categories')->where('id', $category->id)->value('name');

    $this->assertNotSame('Spider-Man', $raw);
    expect(decrypt($raw, false))->toBe('Spider-Man');
});

it('encrypts the description at rest', function () {
    $category = Category::factory()->create(['description' => 'Wall-crawler key issues.']);

    $raw = DB::table('categories')->where('id', $category->id)->value('description');

    $this->assertNotSame('Wall-crawler key issues.', $raw);
    expect(decrypt($raw, false))->toBe('Wall-crawler key issues.');
});

it('has no description by default', function () {
    $category = Category::factory()->create(['description' => null]);

    expect($category->description)->toBeNull();
});

it('soft deletes', function () {
    $category = Category::factory()->create();

    $category->delete();

    $this->assertSoftDeleted($category);
});
