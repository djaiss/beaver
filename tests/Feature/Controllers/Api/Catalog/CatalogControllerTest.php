<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'uuid',
            'name',
            'description',
            'emoji',
            'visibility',
            'currency',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the collections of the account, most recently updated first', function () {
    $user = $this->createUser();
    $older = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Comics',
        'updated_at' => '2025-01-01 10:00:00',
    ]);
    $newer = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Vinyl',
        'updated_at' => '2025-01-02 10:00:00',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collections');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => $this->jsonStructure,
            ],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.id', (string) $newer->id)
        ->assertJsonPath('data.0.attributes.name', 'Vinyl')
        ->assertJsonPath('data.1.id', (string) $older->id)
        ->assertJsonPath('data.1.attributes.name', 'Comics');
});

it('does not list collections from another account', function () {
    $user = $this->createUser();
    Catalog::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collections');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('paginates the collections', function () {
    $user = $this->createUser();
    Catalog::factory()->count(15)->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collections');

    $response
        ->assertOk()
        ->assertJsonCount(10, 'data')
        ->assertJsonPath('meta.total', 15);
});

it('shows a collection', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'My Comics',
        'description' => 'The one with the comic books',
        'emoji' => '📚',
        'visibility' => 'private',
        'currency' => 'USD',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collections/'.$catalog->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'collection')
        ->assertJsonPath('data.id', (string) $catalog->id)
        ->assertJsonPath('data.attributes.name', 'My Comics')
        ->assertJsonPath('data.attributes.description', 'The one with the comic books')
        ->assertJsonPath('data.attributes.emoji', '📚')
        ->assertJsonPath('data.attributes.visibility', 'private')
        ->assertJsonPath('data.attributes.currency', 'USD')
        ->assertJsonPath('data.links.self', route('api.collections.show', $catalog->id));
});

it('returns not found for a collection from another account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collections/'.$catalog->id);

    $response->assertNotFound();
});

it('creates a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collections', [
        'name' => 'My Comics',
        'description' => 'Could this BE any more of a collection?',
        'emoji' => '📚',
        'visibility' => 'private',
        'currency' => 'USD',
        'collection_type_ids' => [$type->id],
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'My Comics')
        ->assertJsonPath('data.attributes.visibility', 'private');

    $catalog = Catalog::query()->first();
    expect($catalog->name)->toBe('My Comics');
    expect($catalog->account_id)->toBe($user->account_id);

    $this->assertDatabaseHas('catalog_type', [
        'catalog_id' => $catalog->id,
        'type_id' => $type->id,
    ]);
});

it('validates the name when creating a collection', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collections', [
        'visibility' => 'private',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts collection creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/collections', [
        'name' => 'My Comics',
        'visibility' => 'private',
    ]);

    $response->assertNotFound();
});

it('updates a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collections/'.$catalog->id, [
        'name' => 'My Vinyl',
        'visibility' => 'shared',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'My Vinyl')
        ->assertJsonPath('data.attributes.visibility', 'shared');

    expect($catalog->refresh()->name)->toBe('My Vinyl');
});

it('restricts collection updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/collections/'.$catalog->id, [
        'name' => 'My Vinyl',
        'visibility' => 'private',
    ]);

    $response->assertNotFound();
});

it('deletes a collection', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/collections/'.$catalog->id);

    $response->assertNoContent();

    $this->assertSoftDeleted('catalogs', [
        'id' => $catalog->id,
    ]);
});

it('restricts collection deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/collections/'.$catalog->id);

    $response->assertNotFound();
});
