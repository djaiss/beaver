<?php

declare(strict_types=1);
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores the view for the acting user', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->put("/collections/{$collection->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ]);

    $response->assertNoContent();
    $this->assertDatabaseHas('collection_views', [
        'user_id' => $user->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);
});

it('lets a viewer store their own view', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->put("/collections/{$collection->id}/item-view", [
        'view' => ItemViewEnum::List->value,
    ])->assertNoContent();

    $this->assertDatabaseHas('collection_views', [
        'user_id' => $viewer->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::List->value,
    ]);
});

it('rejects an invalid view', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)
        ->from("/collections/{$collection->id}")
        ->put("/collections/{$collection->id}/item-view", ['view' => 'carousel'])
        ->assertSessionHasErrors('view');

    $this->assertDatabaseCount('collection_views', 0);
});

it('does not store a view for another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();

    $this->actingAs($user)->put("/collections/{$foreign->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ])->assertNotFound();

    $this->assertDatabaseCount('collection_views', 0);
});

it('requires authentication', function () {
    $collection = Collection::factory()->create();

    $this->put("/collections/{$collection->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ])->assertRedirect('/login');
});
