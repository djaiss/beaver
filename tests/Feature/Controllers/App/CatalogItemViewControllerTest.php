<?php

declare(strict_types=1);
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores the view for the acting user', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->put("/collections/{$catalog->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ]);

    $response->assertNoContent();
    $this->assertDatabaseHas('catalog_views', [
        'user_id' => $user->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);
});

it('lets a viewer store their own view', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->put("/collections/{$catalog->id}/item-view", [
        'view' => ItemViewEnum::List->value,
    ])->assertNoContent();

    $this->assertDatabaseHas('catalog_views', [
        'user_id' => $viewer->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::List->value,
    ]);
});

it('rejects an invalid view', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)
        ->from("/collections/{$catalog->id}")
        ->put("/collections/{$catalog->id}/item-view", ['view' => 'carousel'])
        ->assertSessionHasErrors('view');

    $this->assertDatabaseCount('catalog_views', 0);
});

it('does not store a view for another accounts collection', function () {
    $user = $this->createUser();
    $foreign = Catalog::factory()->create();

    $this->actingAs($user)->put("/collections/{$foreign->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ])->assertNotFound();

    $this->assertDatabaseCount('catalog_views', 0);
});

it('requires authentication', function () {
    $catalog = Catalog::factory()->create();

    $this->put("/collections/{$catalog->id}/item-view", [
        'view' => ItemViewEnum::Table->value,
    ])->assertRedirect('/login');
});
