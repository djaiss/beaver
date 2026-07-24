<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'attributes' => [
            'query',
            'total',
            'matched',
            'truncated',
            'counts',
            'results' => [
                '*' => [
                    'type',
                    'id',
                    'title',
                    'context',
                    'collection_name',
                    'score',
                    'name_match',
                    'links' => [
                        'self',
                    ],
                ],
            ],
        ],
    ];
});

it('searches the account', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Amazing Spider-Man']);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/search?q=spider');

    $response
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'search_results')
        ->assertJsonPath('data.attributes.query', 'spider')
        ->assertJsonPath('data.attributes.total', 1)
        ->assertJsonPath('data.attributes.truncated', false)
        ->assertJsonPath('data.attributes.results.0.type', 'item')
        ->assertJsonPath('data.attributes.results.0.id', (string) $item->id)
        ->assertJsonPath('data.attributes.results.0.title', 'Amazing Spider-Man')
        ->assertJsonPath('data.attributes.results.0.collection_name', 'Comics')
        ->assertJsonPath('data.attributes.results.0.name_match', true);
});

it('names a collection rather than a catalog', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Central Perk Mugs']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/search?q=perk')
        ->assertOk()
        ->assertJsonPath('data.attributes.results.0.type', 'collection');
});

it('narrows the results to one kind of record', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id, 'name' => 'Spider']);
    Item::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider Sense']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/search?q=spider&type=collection')
        ->assertOk()
        ->assertJsonPath('data.attributes.total', 2)
        ->assertJsonPath('data.attributes.matched', 1)
        ->assertJsonCount(1, 'data.attributes.results');
});

it('does not reach into another account', function () {
    $user = $this->createUser();
    Catalog::factory()->create(['name' => 'Central Perk Mugs']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/search?q=perk')
        ->assertOk()
        ->assertJsonPath('data.attributes.total', 0)
        ->assertJsonCount(0, 'data.attributes.results');
});

it('hides a tag from a viewer', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    Tag::factory()->create(['account_id' => $account->id, 'name' => 'Autographed']);

    Sanctum::actingAs($viewer);

    $this->json('GET', '/api/search?q=autographed')
        ->assertOk()
        ->assertJsonPath('data.attributes.total', 0);
});

it('requires something to search for', function () {
    Sanctum::actingAs($this->createUser());

    $this->json('GET', '/api/search')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('q');
});

it('rejects a query longer than the field allows', function () {
    Sanctum::actingAs($this->createUser());

    $this->json('GET', '/api/search?q='.str_repeat('a', 256))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('q');
});

it('rejects an unknown kind of record', function () {
    Sanctum::actingAs($this->createUser());

    $this->json('GET', '/api/search?q=spider&type=spaceship')
        ->assertUnprocessable()
        ->assertJsonValidationErrors('type');
});

it('requires authentication', function () {
    $this->json('GET', '/api/search?q=spider')->assertUnauthorized();
});
