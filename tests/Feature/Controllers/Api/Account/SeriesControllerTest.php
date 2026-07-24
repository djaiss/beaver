<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Item;
use App\Models\Series;
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
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the series of the account', function () {
    $user = $this->createUser();
    Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Potter']);
    Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Star Wars']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/series')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.attributes.name', 'Harry Potter')
        ->assertJsonPath('data.0.type', 'series');
});

it('does not list series from another account', function () {
    $user = $this->createUser();
    Series::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/series')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('paginates the series', function () {
    $user = $this->createUser();
    Series::factory()->count(3)->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/series?per_page=2')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('gets a single series', function () {
    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Pink Floyd']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/series/'.$series->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.name', 'Pink Floyd')
        ->assertJsonPath('data.id', (string) $series->id);
});

it('returns not found for a series of another account', function () {
    $user = $this->createUser();
    $series = Series::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/series/'.$series->id)->assertNotFound();
});

it('creates a series', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/series', [
        'name' => 'The Lord of the Rings',
        'description' => 'Middle-earth across books, films and figures.',
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.name', 'The Lord of the Rings');

    $this->assertDatabaseCount('series', 1);
    expect(Series::first()->account_id)->toBe($user->account_id);
});

it('rejects a series without a name', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/series', ['description' => 'No name.'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('refuses to create a series for a viewer', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/series', ['name' => 'Marvel'])->assertNotFound();
});

it('updates a series', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id, 'name' => 'Harry Poter']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/series/'.$series->id, ['name' => 'Harry Potter'])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Harry Potter');

    expect($series->refresh()->name)->toBe('Harry Potter');
});

it('returns not found when updating a series of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create();

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/series/'.$series->id, ['name' => 'Mine now'])->assertNotFound();
});

it('deletes a series and unlinks its items', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create(['account_id' => $user->account_id]);
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $item = Item::factory()->create(['catalog_id' => $catalog->id, 'series_id' => $series->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/series/'.$series->id)->assertNoContent();

    $this->assertModelMissing($series);
    $this->assertModelExists($item);
    expect($item->refresh()->series_id)->toBeNull();
});

it('returns not found when deleting a series of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $series = Series::factory()->create();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/series/'.$series->id)->assertNotFound();

    $this->assertModelExists($series);
});
