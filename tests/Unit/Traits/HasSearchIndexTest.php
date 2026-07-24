<?php

declare(strict_types=1);
use App\Jobs\ReindexSearchDependents;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('indexes a record the moment it is created', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'collection',
        'searchable_id' => $catalog->id,
        'token' => BlindIndex::hash('perk'),
    ]);
});

it('reindexes a record when it is updated', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);

    $catalog->update(['name' => 'Moondance Diner']);

    $this->assertDatabaseHas('search_tokens', [
        'searchable_id' => $catalog->id,
        'searchable_type' => 'collection',
        'token' => BlindIndex::hash('moondance'),
    ]);

    $this->assertDatabaseMissing('search_tokens', [
        'searchable_id' => $catalog->id,
        'searchable_type' => 'collection',
        'token' => BlindIndex::hash('perk'),
    ]);
});

it('clears the index when a record is soft deleted', function () {
    $item = Item::factory()->create(['name' => 'Smelly Cat']);

    $item->delete();

    expect($item->searchTokens()->count())->toBe(0);
});

it('puts a restored record back in the index', function () {
    $item = Item::factory()->create(['name' => 'Smelly Cat']);
    $item->delete();

    $item->restore();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $item->id,
        'token' => BlindIndex::hash('smelly'),
    ]);
});

it('clears the index when a record is force deleted', function () {
    $item = Item::factory()->create(['name' => 'Smelly Cat']);
    $id = $item->id;

    $item->forceDelete();

    $this->assertDatabaseMissing('search_tokens', [
        'searchable_type' => 'item',
        'searchable_id' => $id,
    ]);
});

it('clears the index when a record without soft deletes is removed', function () {
    $tag = Tag::factory()->create(['name' => 'Signed']);
    $id = $tag->id;

    $tag->delete();

    $this->assertDatabaseMissing('search_tokens', [
        'searchable_type' => 'tag',
        'searchable_id' => $id,
    ]);
});

it('queues a reindex of the dependents when a record is renamed', function () {
    Queue::fake();

    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);

    $catalog->update(['name' => 'Moondance Diner']);

    Queue::assertPushedOn(queue: 'low', job: ReindexSearchDependents::class);
});

it('does not queue a reindex of the dependents when the name is untouched', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk']);

    Queue::fake();

    $catalog->update(['description' => 'Coffee and sofas']);

    Queue::assertNotPushed(ReindexSearchDependents::class);
});
