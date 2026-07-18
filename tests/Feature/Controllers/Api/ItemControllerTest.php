<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'description',
            'collection_id',
            'type_id',
            'category_id',
            'set_id',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the items of a collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Issue 1']);
    Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Issue 2']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/items')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ]);
});

it('does not list items from another account', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create();
    Item::factory()->create(['collection_id' => $collection->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/items')->assertNotFound();
});

it('shows an item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Issue 1']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/items/'.$item->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'item')
        ->assertJsonPath('data.attributes.name', 'Issue 1')
        ->assertJsonPath('data.links.self', route('api.collections.items.show', [$collection->id, $item->id]));
});

it('returns not found for an item from another collection', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collections/'.$collection->id.'/items/'.$item->id)->assertNotFound();
});

it('creates an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collections/'.$collection->id.'/items', [
        'name' => 'Amazing Spider-Man #1',
        'description' => 'Near mint.',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.name', 'Amazing Spider-Man #1')
        ->assertJsonPath('data.attributes.collection_id', (string) $collection->id);

    $item = Item::query()->latest('id')->first();
    expect($item->collection_id)->toBe($collection->id);
    expect($item->name)->toBe('Amazing Spider-Man #1');
});

it('validates the name when creating an item', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collections/'.$collection->id.'/items', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts item creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/collections/'.$collection->id.'/items', ['name' => 'Issue 1'])
        ->assertNotFound();
});

it('updates an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Old name']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collections/'.$collection->id.'/items/'.$item->id, [
        'name' => 'New name',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'New name');

    expect($item->refresh()->name)->toBe('New name');
});

it('restricts item updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collections/'.$collection->id.'/items/'.$item->id, ['name' => 'New name'])
        ->assertNotFound();
});

it('deletes an item', function () {
    Queue::fake();

    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/collections/'.$collection->id.'/items/'.$item->id)->assertNoContent();

    $this->assertSoftDeleted($item);
});

it('restricts item deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/collections/'.$collection->id.'/items/'.$item->id)->assertNotFound();
});
