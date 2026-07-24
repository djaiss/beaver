<?php

declare(strict_types=1);

use App\Models\Catalog;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves an item of the collection in the url', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'The Holiday Armadillo']);

    $this->actingAs($user)
        ->get('/collections/'.$catalog->id.'/items/'.$item->id)
        ->assertOk()
        ->assertSee('The Holiday Armadillo');
});

it('does not find an item that belongs to another collection of the same account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $other = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $other->id]);

    $this->actingAs($user)
        ->get('/collections/'.$catalog->id.'/items/'.$item->id)
        ->assertNotFound();
});

it('does not find an item of another account', function () {
    $user = $this->createUser();
    $stranger = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $stranger->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id]);

    $this->actingAs($user)
        ->get('/collections/'.$catalog->id.'/items/'.$item->id)
        ->assertNotFound();
});

it('does not find an item that does not exist', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)
        ->get('/collections/'.$catalog->id.'/items/404404')
        ->assertNotFound();
});
