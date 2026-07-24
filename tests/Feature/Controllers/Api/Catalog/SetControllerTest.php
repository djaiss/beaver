<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\Set;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'catalog_id',
            'name',
            'description',
            'target_count',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the sets of the account', function () {
    $user = $this->createUser();
    Set::factory()->forAccount($user->account_id)->create(['name' => 'First set']);
    Set::factory()->forAccount($user->account_id)->create(['name' => 'Second set']);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/sets');

    $response
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.attributes.name', 'First set');
});

it('does not list sets from another account', function () {
    $user = $this->createUser();
    Set::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/sets')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows a set', function () {
    $user = $this->createUser();
    $set = Set::factory()->forAccount($user->account_id)->create(['name' => 'Spider-Man run']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/sets/'.$set->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'set')
        ->assertJsonPath('data.id', (string) $set->id)
        ->assertJsonPath('data.attributes.name', 'Spider-Man run')
        ->assertJsonPath('data.links.self', route('api.sets.show', $set->id));
});

it('returns not found for a set from another account', function () {
    $user = $this->createUser();
    $set = Set::factory()->create();

    Sanctum::actingAs($user);

    $this->json('GET', '/api/sets/'.$set->id)->assertNotFound();
});

it('creates a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/sets', [
        'catalog_id' => $catalog->id,
        'name' => 'Spider-Man run',
        'description' => 'Issues 1 to 10.',
        'target_count' => 10,
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.name', 'Spider-Man run')
        ->assertJsonPath('data.attributes.description', 'Issues 1 to 10.')
        ->assertJsonPath('data.attributes.target_count', 10);

    $set = Set::query()->latest('id')->first();
    expect($set->name)->toBe('Spider-Man run');
    expect($set->catalog_id)->toBe($catalog->id);
});

it('validates the name when creating a set', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/sets', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts set creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/sets', [
        'catalog_id' => $catalog->id,
        'name' => 'Spider-Man run',
    ])->assertNotFound();
});

it('updates a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $set = Set::factory()->forAccount($user->account_id)->create(['name' => 'Old name']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/sets/'.$set->id, [
        'name' => 'New name',
        'description' => 'Updated.',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'New name')
        ->assertJsonPath('data.attributes.description', 'Updated.');

    expect($set->refresh()->name)->toBe('New name');
});

it('restricts set updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $set = Set::factory()->forAccount($account->id)->create();

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/sets/'.$set->id, ['name' => 'New name'])->assertNotFound();
});

it('deletes a set', function () {
    Queue::fake();

    $user = $this->createUser();
    $set = Set::factory()->forAccount($user->account_id)->create();

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/sets/'.$set->id)->assertNoContent();

    $this->assertSoftDeleted($set);
});

it('restricts set deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $set = Set::factory()->forAccount($account->id)->create();

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/sets/'.$set->id)->assertNotFound();
});
