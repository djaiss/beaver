<?php

declare(strict_types=1);
use App\Actions\IndexItemPhotoSearchTokens;
use App\Jobs\ReindexItemPhotos;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('reindexes every photo of the item under the new name', function () {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Barista']);
    $first = ItemPhoto::factory()->create(['item_id' => $item->id, 'filename' => 'one.jpg']);
    $second = ItemPhoto::factory()->create(['item_id' => $item->id, 'filename' => 'two.jpg']);
    new IndexItemPhotoSearchTokens(itemPhoto: $first)->execute();
    new IndexItemPhotoSearchTokens(itemPhoto: $second)->execute();

    $item->update(['name' => 'Manager']);
    new ReindexItemPhotos($item)->handle();

    foreach ([$first, $second] as $photo) {
        $this->assertDatabaseHas('item_photo_search_tokens', [
            'item_photo_id' => $photo->id,
            'token' => BlindIndex::hash('manager'),
        ]);
        $this->assertDatabaseMissing('item_photo_search_tokens', [
            'item_photo_id' => $photo->id,
            'token' => BlindIndex::hash('barista'),
        ]);
    }
});

it('leaves the photos of another item alone', function () {
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Barista']);
    $other = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Chef']);
    $photo = ItemPhoto::factory()->create(['item_id' => $other->id, 'filename' => 'chef.jpg']);
    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    new ReindexItemPhotos($item)->handle();

    $this->assertDatabaseHas('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('chef'),
    ]);
});

it('does nothing for an item without photos', function () {
    $item = Item::factory()->create();

    new ReindexItemPhotos($item)->handle();

    $this->assertDatabaseCount('item_photo_search_tokens', 0);
});
