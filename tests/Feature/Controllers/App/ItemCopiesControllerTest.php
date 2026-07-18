<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the copies of an item with their condition and location', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'currency' => 'USD']);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $condition = Condition::factory()->create(['account_id' => $user->account_id, 'name' => 'Near Mint']);
    $location = Location::factory()->create(['account_id' => $user->account_id, 'name' => 'Display Case']);
    Copy::factory()->create([
        'item_id' => $item->id,
        'condition_id' => $condition->id,
        'location_id' => $location->id,
        'price_paid' => 18000,
        'estimated_value' => 42000,
    ]);

    $response = $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]));

    $response->assertOk();
    $response->assertSee('Near Mint');
    $response->assertSee('Display Case');
    $response->assertSee('$420');
    $response->assertSee('$180');
});

it('shows the provenance placeholder on a copy', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('Provenance');
});

it('tells the reader when an item has no copies', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('No copies of this item yet.');
});

it('marks the copies tab as the current page', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))
        ->assertOk()
        ->assertSee('data-test="item-tab-copies"', false)
        ->assertSee('aria-current="page"', false);
});

it('lets a viewer read the copies of an item', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($viewer)->get(route('items.copies.index', [$collection, $item]))->assertOk();
});

it('does not show the copies of an item belonging to another account', function () {
    $user = $this->createUser();
    $foreign = Collection::factory()->create();
    $item = Item::factory()->create(['collection_id' => $foreign->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$foreign, $item]))->assertNotFound();
});

it('does not show the copies of an item that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)->get(route('items.copies.index', [$collection, $item]))->assertNotFound();
});
