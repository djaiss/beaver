<?php

declare(strict_types=1);
use App\Jobs\ReindexSearchDependents;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reindexes the items of a renamed collection', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);

    $catalog->name = 'Moondance Diner';
    $catalog->saveQuietly();

    new ReindexSearchDependents(searchable: $catalog->fresh())->handle();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('moondance'),
    ]);

    $this->assertDatabaseMissing('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('perk'),
    ]);
});

it('reindexes the items of a renamed tag', function () {
    $tag = Tag::factory()->create(['name' => 'Signed']);
    $item = Item::factory()->create(['name' => 'Smelly Cat']);
    $item->tags()->attach($tag->id);

    $tag->name = 'Autographed';
    $tag->saveQuietly();

    new ReindexSearchDependents(searchable: $tag->fresh())->handle();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('autographed'),
    ]);
});

it('does nothing for a record nothing quotes', function () {
    $item = Item::factory()->create(['name' => 'Smelly Cat']);

    new ReindexSearchDependents(searchable: $item)->handle();

    expect($item->searchTokens()->count())->toBeGreaterThan(0);
});
