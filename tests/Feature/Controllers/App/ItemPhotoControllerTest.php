<?php

declare(strict_types=1);
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function makePhoto(Catalog $catalog): ItemPhoto
{
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
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
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $photo = makePhoto($catalog);

    $response = $this->actingAs($user)->get("/items/photos/{$photo->id}");

    $response->assertOk();
    expect($response->headers->get('content-type'))->toContain('image/jpeg');
});

it('does not serve a photo from another account', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $foreignCatalog = Catalog::factory()->create();
    $photo = makePhoto($foreignCatalog);

    $this->actingAs($user)->get("/items/photos/{$photo->id}")->assertNotFound();
});

it('returns not found when the file is missing from disk', function () {
    Storage::fake(config('filesystems.default'));
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id, 'path' => 'items/none.jpg']);

    $this->actingAs($user)->get("/items/photos/{$photo->id}")->assertNotFound();
});

it('requires authentication', function () {
    Storage::fake(config('filesystems.default'));
    $catalog = Catalog::factory()->create();
    $photo = makePhoto($catalog);

    $this->get("/items/photos/{$photo->id}")->assertRedirect('/login');
});
