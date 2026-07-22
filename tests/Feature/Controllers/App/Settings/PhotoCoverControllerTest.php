<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('makes the photo the cover of its item', function () {
    Queue::fake();
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $cover = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true, 'position' => 1]);
    $other = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => false, 'position' => 2]);

    $this->actingAs($user)
        ->put("/settings/photos/{$other->id}/cover")
        ->assertRedirect('/settings/photos');

    expect($other->fresh()->is_main)->toBeTrue();
    expect($cover->fresh()->is_main)->toBeFalse();
});

it('does not touch a photo of another account', function () {
    Queue::fake();
    $user = $this->createUser();
    $photo = ItemPhoto::factory()->create();

    $this->actingAs($user)->put("/settings/photos/{$photo->id}/cover")->assertNotFound();

    expect($photo->fresh()->is_main)->toBeFalse();
});

it('keeps a viewer out', function () {
    Queue::fake();
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount(user: $this->createUser(), account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $photo = ItemPhoto::factory()->create(['item_id' => $item->id]);

    $this->actingAs($viewer)->put("/settings/photos/{$photo->id}/cover")->assertNotFound();
});

it('requires authentication', function () {
    $photo = ItemPhoto::factory()->create();

    $this->put("/settings/photos/{$photo->id}/cover")->assertRedirect('/login');
});
