<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * Create an item belonging to the given account, for the photo selection tests.
 */
function photoSelectionTestItem(int $accountId): Item
{
    $collection = Collection::factory()->create(['account_id' => $accountId]);

    return Item::factory()->create(['collection_id' => $collection->id]);
}

it('deletes several photos in one call', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $item = photoSelectionTestItem($user->account_id);
    $first = ItemPhoto::factory()->create(['item_id' => $item->id, 'position' => 1]);
    $second = ItemPhoto::factory()->create(['item_id' => $item->id, 'position' => 2]);
    $kept = ItemPhoto::factory()->create(['item_id' => $item->id, 'position' => 3]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/photos', [
        'photo_ids' => [$first->id, $second->id],
    ])
        ->assertNoContent();

    $this->assertModelMissing($first);
    $this->assertModelMissing($second);
    $this->assertModelExists($kept);
});

it('validates the photo ids when deleting several photos', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/photos', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['photo_ids']);
});

it('rejects photo ids that are not integers', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/photos', [
        'photo_ids' => ['gunther'],
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['photo_ids.0']);
});

it('returns not found when a photo belongs to another account', function () {
    Queue::fake();
    Storage::fake('local');

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $mine = ItemPhoto::factory()->create(['item_id' => photoSelectionTestItem($user->account_id)->id]);
    $theirs = ItemPhoto::factory()->create(['item_id' => photoSelectionTestItem($otherAccount->id)->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/photos', [
        'photo_ids' => [$mine->id, $theirs->id],
    ])
        ->assertNotFound();

    $this->assertModelExists($mine);
    $this->assertModelExists($theirs);
});

it('restricts deleting several photos to owners and editors', function () {
    Queue::fake();
    Storage::fake('local');

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $photo = ItemPhoto::factory()->create(['item_id' => photoSelectionTestItem($account->id)->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/photos', [
        'photo_ids' => [$photo->id],
    ])
        ->assertNotFound();

    $this->assertModelExists($photo);
});
