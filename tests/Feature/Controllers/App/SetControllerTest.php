<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the sets of a collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    Set::factory()->create(['collection_id' => $collection->id, 'name' => 'Amazing Spider-Man #1-10']);
    Set::factory()->create(['collection_id' => $collection->id, 'name' => 'X-Men Vol. 2 #1-20']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSee('Amazing Spider-Man #1-10')
        ->assertSee('X-Men Vol. 2 #1-20')
        ->assertSee('2 sets');
});

it('shows how far a set is from complete', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man run', 'target_count' => 10]);
    Item::factory()->count(2)->create(['collection_id' => $collection->id, 'set_id' => $set->id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSee('2 of 10 owned')
        ->assertSee('20% complete')
        ->assertSee('8 missing');
});

it('marks a set as complete once the target is reached', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id, 'target_count' => 2]);
    Item::factory()->count(2)->create(['collection_id' => $collection->id, 'set_id' => $set->id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSee('100% complete')
        ->assertSee('Complete');
});

it('shows a plain item count for a set without a target', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id, 'target_count' => null]);
    Item::factory()->count(3)->create(['collection_id' => $collection->id, 'set_id' => $set->id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSee('3 items')
        ->assertSee('No target');
});

it('shows a breadcrumb back to the collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id, 'name' => 'Marvel Comics 1990s']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSeeInOrder(['Collections', 'Marvel Comics 1990s', 'Sets'])
        ->assertSee(route('collections.index'), false)
        ->assertSee(route('collections.show', $collection->id), false);
});

it('shows the empty state when the collection has no sets', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()
        ->assertSee('No sets yet')
        ->assertSee('Example set');
});

it('does not list the sets of another accounts collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create();

    $this->actingAs($user)->get('/collections/'.$collection->id.'/sets')->assertNotFound();
});

it('does not list the sets of another collection in the same account', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    Set::factory()->create(['collection_id' => $other->id, 'name' => 'Belongs elsewhere']);

    $response = $this->actingAs($user)->get('/collections/'.$collection->id.'/sets');

    $response->assertOk()->assertDontSee('Belongs elsewhere');
});

it('allows a viewer to list sets', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->get('/collections/'.$collection->id.'/sets')->assertOk();
});

it('creates a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $response = $this->actingAs($user)->post('/collections/'.$collection->id.'/sets', [
        'name' => 'Amazing Spider-Man #1-10',
        'description' => 'The first ten issues.',
        'target_count' => '10',
    ]);

    $response->assertRedirect('/collections/'.$collection->id.'/sets')
        ->assertSessionHas('status', 'Set created');

    $set = Set::query()->latest('id')->first();
    expect($set->name)->toBe('Amazing Spider-Man #1-10');
    expect($set->description)->toBe('The first ten issues.');
    expect($set->target_count)->toBe(10);
    expect($set->collection_id)->toBe($collection->id);
});

it('creates a set without a target', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post('/collections/'.$collection->id.'/sets', [
        'name' => 'Want list',
    ])->assertRedirect();

    expect(Set::query()->latest('id')->first()->target_count)->toBeNull();
});

it('refuses a name that is missing', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post('/collections/'.$collection->id.'/sets', [])
        ->assertSessionHasErrors('name');

    expect(Set::query()->count())->toBe(0);
});

it('refuses a target that is not a positive number', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $this->actingAs($user)->post('/collections/'.$collection->id.'/sets', [
        'name' => 'Want list',
        'target_count' => '0',
    ])->assertSessionHasErrors('target_count');

    expect(Set::query()->count())->toBe(0);
});

it('refuses to create a set in another accounts collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create();

    $this->actingAs($user)->post('/collections/'.$collection->id.'/sets', ['name' => 'Want list'])
        ->assertNotFound();

    expect(Set::query()->count())->toBe(0);
});

it('refuses to let a viewer create a set', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $this->actingAs($viewer)->post('/collections/'.$collection->id.'/sets', ['name' => 'Want list'])
        ->assertNotFound();

    expect(Set::query()->count())->toBe(0);
});

it('updates a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id, 'name' => 'Old name', 'target_count' => 5]);

    $response = $this->actingAs($user)->put('/collections/'.$collection->id.'/sets/'.$set->id, [
        'name' => 'New name',
        'target_count' => '20',
    ]);

    $response->assertRedirect('/collections/'.$collection->id.'/sets')
        ->assertSessionHas('status', 'Set updated');

    $set->refresh();
    expect($set->name)->toBe('New name');
    expect($set->target_count)->toBe(20);
});

it('refuses to update a set of another accounts collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create();
    $set = Set::factory()->create(['collection_id' => $collection->id, 'name' => 'Old name']);

    $this->actingAs($user)->put('/collections/'.$collection->id.'/sets/'.$set->id, ['name' => 'New name'])
        ->assertNotFound();

    expect($set->refresh()->name)->toBe('Old name');
});

it('refuses to update a set that belongs to a different collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $other = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $other->id, 'name' => 'Old name']);

    $this->actingAs($user)->put('/collections/'.$collection->id.'/sets/'.$set->id, ['name' => 'New name'])
        ->assertNotFound();

    expect($set->refresh()->name)->toBe('Old name');
});

it('deletes a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id]);

    $response = $this->actingAs($user)->delete('/collections/'.$collection->id.'/sets/'.$set->id);

    $response->assertRedirect('/collections/'.$collection->id.'/sets')
        ->assertSessionHas('status', 'Set deleted');

    $this->assertSoftDeleted($set);
});

it('keeps the items when their set is deleted', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $set = Set::factory()->create(['collection_id' => $collection->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'set_id' => $set->id]);

    $this->actingAs($user)->delete('/collections/'.$collection->id.'/sets/'.$set->id)->assertRedirect();

    $this->assertModelExists($item);
});

it('refuses to let a viewer delete a set', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $set = Set::factory()->create(['collection_id' => $collection->id]);

    $this->actingAs($viewer)->delete('/collections/'.$collection->id.'/sets/'.$set->id)->assertNotFound();

    $this->assertNotSoftDeleted($set);
});
