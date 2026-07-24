<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create an item belonging to the given account, for the photo tests.
 */
function photoTestItem(int $accountId): Item
{
    $catalog = Catalog::factory()->create(['account_id' => $accountId]);

    return Item::factory()->create(['catalog_id' => $catalog->id]);
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'item_id',
            'filename',
            'mime_type',
            'size',
            'is_main',
            'position',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the photos of an item', function () {
    $user = $this->createUser();
    $item = photoTestItem($user->account_id);
    ItemPhoto::factory()->create(['item_id' => $item->id, 'position' => 1]);
    ItemPhoto::factory()->create(['item_id' => $item->id, 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/photos')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list photos of an item from another account', function () {
    $user = $this->createUser();
    $item = photoTestItem($this->createAccount()->id);
    ItemPhoto::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/photos')->assertNotFound();
});

it('shows a photo', function () {
    $user = $this->createUser();
    $item = photoTestItem($user->account_id);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/items/'.$item->id.'/photos/'.$photo->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'item_photo')
        ->assertJsonPath('data.id', (string) $photo->id)
        ->assertJsonPath('data.links.self', route('api.items.photos.show', [$item->id, $photo->id]));
});

it('uploads a photo', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $item = photoTestItem($user->account_id);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/items/'.$item->id.'/photos', [
        'file' => UploadedFile::fake()->image('cover.jpg', 200, 200),
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.item_id', (string) $item->id)
        ->assertJsonPath('data.attributes.is_main', true);

    expect($item->photos()->count())->toBe(1);
});

it('validates that a file is provided when uploading', function () {
    $user = $this->createUser();
    $item = photoTestItem($user->account_id);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/items/'.$item->id.'/photos', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['file']);
});

it('restricts photo upload to owners and editors', function () {
    Storage::fake('local');

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = photoTestItem($account->id);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/items/'.$item->id.'/photos', [
        'file' => UploadedFile::fake()->image('cover.jpg'),
    ])->assertNotFound();
});

it('sets a photo as the main visual', function () {
    Queue::fake();

    $user = $this->createUser();
    $item = photoTestItem($user->account_id);
    $first = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true, 'position' => 1]);
    $second = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => false, 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/items/'.$item->id.'/photos/'.$second->id.'/main')
        ->assertOk()
        ->assertJsonPath('data.attributes.is_main', true);

    expect($second->refresh()->is_main)->toBeTrue();
    expect($first->refresh()->is_main)->toBeFalse();
});

it('restricts setting the main photo to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = photoTestItem($account->id);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/items/'.$item->id.'/photos/'.$photo->id.'/main')->assertNotFound();
});

it('deletes a photo', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $item = photoTestItem($user->account_id);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/items/'.$item->id.'/photos/'.$photo->id)->assertNoContent();

    $this->assertModelMissing($photo);
});

it('restricts photo deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $item = photoTestItem($account->id);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/items/'.$item->id.'/photos/'.$photo->id)->assertNotFound();
});
