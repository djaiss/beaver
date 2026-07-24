<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
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
            'name',
            'color',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the collection types of the account, most recently updated first', function () {
    $user = $this->createUser();
    $older = CatalogType::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Comics',
        'updated_at' => '2025-01-01 10:00:00',
    ]);
    $newer = CatalogType::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Wine',
        'updated_at' => '2025-01-02 10:00:00',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types');

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
        ->assertJsonPath('data.0.attributes.name', 'Wine')
        ->assertJsonPath('data.1.id', (string) $older->id)
        ->assertJsonPath('data.1.attributes.name', 'Comics');
});

it('does not list collection types from another account', function () {
    $user = $this->createUser();
    CatalogType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows a collection type', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Comics',
        'color' => '#fb923c',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'collection_type')
        ->assertJsonPath('data.id', (string) $type->id)
        ->assertJsonPath('data.attributes.name', 'Comics')
        ->assertJsonPath('data.attributes.color', '#fb923c')
        ->assertJsonPath('data.links.self', route('api.catalogTypes.show', $type->id));
});

it('returns not found for a collection type from another account', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id);

    $response->assertNotFound();
});

it('creates a collection type', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types', [
        'name' => 'Comics',
        'color' => '#fb923c',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Comics')
        ->assertJsonPath('data.attributes.color', '#fb923c');

    $type = CatalogType::query()->first();
    expect($type->name)->toBe('Comics');
    expect($type->account_id)->toBe($user->account_id);
});

it('validates the color when creating a collection type', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types', [
        'name' => 'Comics',
        'color' => 'not-a-color',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['color']);
});

it('restricts collection type creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/collection-types', [
        'name' => 'Comics',
    ]);

    $response->assertNotFound();
});

it('updates a collection type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id, [
        'name' => 'Vinyl',
        'color' => '#8b5cf6',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Vinyl')
        ->assertJsonPath('data.attributes.color', '#8b5cf6');

    expect($type->refresh()->name)->toBe('Vinyl');
});

it('restricts collection type updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id, [
        'name' => 'Vinyl',
        'color' => '#8b5cf6',
    ]);

    $response->assertNotFound();
});

it('deletes a collection type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/collection-types/'.$type->id);

    $response->assertNoContent();

    $this->assertModelMissing($type);
});

it('restricts collection type deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/collection-types/'.$type->id);

    $response->assertNotFound();
});
