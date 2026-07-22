<?php

declare(strict_types=1);
use App\Actions\DestroyItemPhoto;
use App\Actions\IndexItemPhotoSearchTokens;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Services\BlindIndex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function photoNamed(string $filename, string $itemName): ItemPhoto
{
    $collection = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => $itemName]);

    return ItemPhoto::factory()->create(['item_id' => $item->id, 'filename' => $filename]);
}

it('indexes the words of the file name', function () {
    $photo = photoNamed('central_perk_sign.jpg', 'The Coffee House');

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    $this->assertDatabaseHas('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('perk'),
    ]);
});

it('indexes the name of the item the photo belongs to', function () {
    $photo = photoNamed('img_0001.jpg', 'Smelly Cat');

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    $this->assertDatabaseHas('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('smelly'),
    ]);
});

it('indexes prefixes, so a partial query finds the photo', function () {
    $photo = photoNamed('gunther.png', 'Barista');

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    $this->assertDatabaseHas('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('gunt'),
    ]);
});

it('replaces the previous hashes rather than adding to them', function () {
    $photo = photoNamed('gunther.png', 'Barista');

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();
    $firstCount = $photo->searchTokens()->count();

    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    expect($photo->searchTokens()->count())->toBe($firstCount);
});

it('drops the hashes of a name that is no longer used', function () {
    $photo = photoNamed('gunther.png', 'Barista');
    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    $photo->item->update(['name' => 'Manager']);
    new IndexItemPhotoSearchTokens(itemPhoto: $photo->fresh())->execute();

    $this->assertDatabaseMissing('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('barista'),
    ]);
    $this->assertDatabaseHas('item_photo_search_tokens', [
        'item_photo_id' => $photo->id,
        'token' => BlindIndex::hash('manager'),
    ]);
});

it('takes the hashes with the photo when it is deleted', function () {
    Queue::fake();
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Barista']);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'filename' => 'gunther.png']);
    new IndexItemPhotoSearchTokens(itemPhoto: $photo)->execute();

    new DestroyItemPhoto(user: $user, itemPhoto: $photo)->execute();

    $this->assertDatabaseMissing('item_photo_search_tokens', ['item_photo_id' => $photo->id]);
});
