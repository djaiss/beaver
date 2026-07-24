<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('puts a tag on an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $tag = Tag::factory()->create(['account_id' => $user->account_id, 'name' => 'Signed']);

    $response = $this->actingAs($user)
        ->post("/collections/{$catalog->id}/items/{$item->id}/tags", ['name' => 'Signed']);

    $response->assertRedirect("/collections/{$catalog->id}/items/{$item->id}");
    $response->assertSessionHas('status', 'Tag added');

    expect($item->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

it('creates a tag the account does not have yet', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)
        ->post("/collections/{$catalog->id}/items/{$item->id}/tags", ['name' => 'First print'])
        ->assertRedirect("/collections/{$catalog->id}/items/{$item->id}");

    $tag = Tag::query()->first();
    expect($tag->name)->toBe('First print');
    expect($tag->account_id)->toBe($user->account_id);
    expect($item->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

it('requires a name to put a tag on an item', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)
        ->post("/collections/{$catalog->id}/items/{$item->id}/tags", ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('takes a tag off an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $tag = Tag::factory()->create(['account_id' => $user->account_id]);
    $item->tags()->sync([$tag->id]);

    $response = $this->actingAs($user)
        ->delete("/collections/{$catalog->id}/items/{$item->id}/tags/{$tag->id}");

    $response->assertRedirect("/collections/{$catalog->id}/items/{$item->id}");
    $response->assertSessionHas('status', 'Tag removed');

    expect($item->tags()->count())->toBe(0);
    $this->assertDatabaseHas('tags', ['id' => $tag->id]);
});

it('does not tag an item belonging to another account', function () {
    $user = $this->createUser();
    $foreignCatalog = Catalog::factory()->create();
    $foreignItem = Item::factory()->create(['catalog_id' => $foreignCatalog->id]);

    $this->actingAs($user)
        ->post("/collections/{$foreignCatalog->id}/items/{$foreignItem->id}/tags", ['name' => 'Signed'])
        ->assertNotFound();
});

it('does not take off a tag belonging to another account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $foreignTag = Tag::factory()->create();

    $this->actingAs($user)
        ->delete("/collections/{$catalog->id}/items/{$item->id}/tags/{$foreignTag->id}")
        ->assertNotFound();
});

it('does not let a viewer tag an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($viewer)
        ->post("/collections/{$catalog->id}/items/{$item->id}/tags", ['name' => 'Signed'])
        ->assertNotFound();
});

it('does not let a viewer take a tag off an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);
    $item->tags()->sync([$tag->id]);

    $this->actingAs($viewer)
        ->delete("/collections/{$catalog->id}/items/{$item->id}/tags/{$tag->id}")
        ->assertNotFound();

    expect($item->tags()->count())->toBe(1);
});
