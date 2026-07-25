<?php

declare(strict_types=1);
use App\Models\Catalog;
use App\Models\Item;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('rebuilds hashes that went missing behind the applications back', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk Mugs']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);

    DB::table('search_tokens')->delete();

    $this->artisan('search:rebuild-index')
        ->expectsOutputToContain('record(s) indexed.')
        ->assertSuccessful();

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'collection',
        'searchable_id' => $catalog->id,
        'token' => BlindIndex::hash('perk'),
    ]);

    $this->assertDatabaseHas('search_tokens', [
        'searchable_type' => 'item',
        'token' => BlindIndex::hash('smelly'),
    ]);
});

it('rebuilds one kind of record only', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk Mugs']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Smelly Cat']);

    DB::table('search_tokens')->delete();

    $this->artisan('search:rebuild-index', ['--type' => 'collection'])->assertSuccessful();

    $this->assertDatabaseHas('search_tokens', ['searchable_type' => 'collection']);
    $this->assertDatabaseMissing('search_tokens', ['searchable_type' => 'item']);
});

it('refuses a kind of record it does not know', function () {
    $this->artisan('search:rebuild-index', ['--type' => 'spaceship'])
        ->expectsOutputToContain('Unknown type.')
        ->assertFailed();
});

it('is safe to run twice', function () {
    $catalog = Catalog::factory()->create(['name' => 'Central Perk Mugs']);

    $this->artisan('search:rebuild-index')->assertSuccessful();
    $first = $catalog->searchTokens()->count();

    $this->artisan('search:rebuild-index')->assertSuccessful();

    expect($catalog->searchTokens()->count())->toBe($first);
});
