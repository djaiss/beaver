<?php

declare(strict_types=1);

use App\Models\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('resolves a collection of the account and shares it with the views', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Friends memorabilia',
    ]);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/categories')
        ->assertOk()
        ->assertSee('Friends memorabilia');
});

it('does not find a collection of another account', function () {
    $user = $this->createUser();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $stranger->account_id]);

    $this->actingAs($user)
        ->get('/collections/'.$collection->id.'/categories')
        ->assertNotFound();
});

it('does not find a collection that does not exist', function () {
    $user = $this->createUser();

    $this->actingAs($user)
        ->get('/collections/404404/categories')
        ->assertNotFound();
});

/*
 * Laravel would otherwise resolve the models the controllers type hint on its
 * own, by id alone, before the middleware had a say. The lookup has to stay the
 * account scoped one.
 */
it('does not let laravel resolve the collection before the middleware does', function () {
    $user = $this->createUser();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $stranger->account_id]);
    $category = $collection->categories()->create(['name' => 'Central Perk']);

    $this->actingAs($user)
        ->put('/collections/'.$collection->id.'/categories/'.$category->id, ['name' => 'Renamed'])
        ->assertNotFound();

    expect($category->fresh()->name)->toBe('Central Perk');
});
