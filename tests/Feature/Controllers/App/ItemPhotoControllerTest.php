<?php

declare(strict_types=1);
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makePhoto(Collection $collection): ItemPhoto
{
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $path = "items/{$item->id}/cover.jpg";
    Storage::disk(config('filesystems.default'))->put($path, 'binary-image-bytes');

    return ItemPhoto::factory()->create([
        'item_id' => $item->id,
        'path' => $path,
        'mime_type' => 'image/jpeg',
    ]);
}

it('streams a photo to a member of the account', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $photo = makePhoto($collection);

    $response = $this->actingAs($user)->get("/items/photos/{$photo->id}");

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('image/jpeg');
});

it('does not serve a photo from another account', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $foreignCollection = Collection::factory()->create();
    $photo = makePhoto($foreignCollection);

    $this->actingAs($user)->get("/items/photos/{$photo->id}")->assertNotFound();
});

it('returns not found when the file is missing from disk', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'path' => 'items/none.jpg']);

    $this->actingAs($user)->get("/items/photos/{$photo->id}")->assertNotFound();
});

it('requires authentication', function () {
    Storage::fake(config('filesystems.default'));
    $collection = Collection::factory()->create();
    $photo = makePhoto($collection);

    $this->get("/items/photos/{$photo->id}")->assertRedirect('/login');
});
