<?php

declare(strict_types=1);
use App\Actions\IndexSearchable;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('indexes the name of the record at the heaviest weight', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk Mugs']);

    new IndexSearchable(searchable: $catalog)->execute();

    $this->assertDatabaseHas('search_tokens', [
        'account_id' => $catalog->account_id,
        'searchable_type' => 'collection',
        'searchable_id' => $catalog->id,
        'token' => BlindIndex::hash('perk'),
        'weight' => IndexSearchable::WEIGHT_TITLE,
    ]);
});

it('indexes the description at the lightest weight', function () {
    $catalog = Catalog::factory()->create(['name' => 'Mugs', 'description' => 'Everything from the coffee house']);

    new IndexSearchable(searchable: $catalog)->execute();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_id' => $catalog->id,
        'token' => BlindIndex::hash('coffee'),
        'weight' => IndexSearchable::WEIGHT_TEXT,
    ]);
});

it('keeps the heaviest weight for a word that appears twice', function () {
    $catalog = Catalog::factory()->create(['name' => 'Gunther', 'description' => 'Gunther loves Rachel']);

    new IndexSearchable(searchable: $catalog)->execute();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_id' => $catalog->id,
        'token' => BlindIndex::hash('gunther'),
        'weight' => IndexSearchable::WEIGHT_TITLE,
    ]);
});

it('indexes the words filed around an item', function () {
    $catalog = Catalog::factory()->create(['name' => 'Marvel Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);
    $tag = Tag::factory()->create(['account_id' => $catalog->account_id, 'name' => 'Signed']);
    $item->tags()->attach($tag->id);

    new IndexSearchable(searchable: $item->fresh())->execute();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('marvel'),
        'weight' => IndexSearchable::WEIGHT_RELATED,
    ]);

    $this->assertDatabaseHas('search_tokens', [
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('signed'),
        'weight' => IndexSearchable::WEIGHT_RELATED,
    ]);
});

it('is idempotent', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);

    new IndexSearchable(searchable: $catalog)->execute();
    $first = $catalog->searchTokens()->count();

    new IndexSearchable(searchable: $catalog)->execute();

    expect($catalog->searchTokens()->count())->toBe($first);
});

it('replaces the hashes of a renamed record', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);
    new IndexSearchable(searchable: $catalog)->execute();

    $catalog->name = 'Moondance Diner';
    $catalog->save();

    new IndexSearchable(searchable: $catalog)->execute();

    $this->assertDatabaseMissing('search_tokens', [
        'searchable_id' => $catalog->id,
        'searchable_type' => 'collection',
        'token' => BlindIndex::hash('perk'),
    ]);
});

it('indexes nothing when the account cannot be resolved', function () {
    $item = Item::factory()->make(['catalog_id' => null]);
    $item->id = 9999;

    new IndexSearchable(searchable: $item)->execute();

    $this->assertDatabaseMissing('search_tokens', ['searchable_id' => 9999]);
});
