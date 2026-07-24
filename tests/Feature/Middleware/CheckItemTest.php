<?php

declare(strict_types=1);

use App\Models\Collection;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves an item of the collection in the url', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'The Holiday Armadillo']);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/items/'.$item->id)
        ->assertOk()
        ->assertSee('The Holiday Armadillo');
});

it('does not find an item that belongs to another collection of the same account', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $other->id]);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/items/'.$item->id)
        ->assertNotFound();
});

it('does not find an item of another account', function () {
    $user = $this->createUser();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $stranger->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/items/'.$item->id)
        ->assertNotFound();
});

it('does not find an item that does not exist', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/items/404404')
        ->assertNotFound();
});
