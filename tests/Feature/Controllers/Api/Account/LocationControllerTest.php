<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Location;
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
            'emoji',
            'parent_id',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the locations of the account', function () {
    $user = $this->createUser();
    $shelf = Location::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Shelf A',
    ]);
    Location::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Box 1',
        'parent_id' => $shelf->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/locations');

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
        ->assertJsonPath('data.0.attributes.name', 'Shelf A')
        ->assertJsonPath('data.1.attributes.name', 'Box 1')
        ->assertJsonPath('data.1.attributes.parent_id', (string) $shelf->id);
});

it('does not list locations from another account', function () {
    $user = $this->createUser();
    Location::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/locations');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows a location', function () {
    $user = $this->createUser();
    $location = Location::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Shelf A',
        'emoji' => '📦',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/locations/'.$location->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'location')
        ->assertJsonPath('data.id', (string) $location->id)
        ->assertJsonPath('data.attributes.name', 'Shelf A')
        ->assertJsonPath('data.attributes.emoji', '📦')
        ->assertJsonPath('data.attributes.parent_id', null)
        ->assertJsonPath('data.links.self', route('api.locations.show', $location->id));
});

it('returns not found for a location from another account', function () {
    $user = $this->createUser();
    $location = Location::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/locations/'.$location->id);

    $response->assertNotFound();
});

it('creates a location', function () {
    Queue::fake();

    $user = $this->createUser();
    $parent = Location::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/locations', [
        'name' => 'Box 1',
        'parent_id' => $parent->id,
        'emoji' => '📦',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Box 1')
        ->assertJsonPath('data.attributes.parent_id', (string) $parent->id);

    $location = Location::query()->latest('id')->first();
    expect($location->name)->toBe('Box 1');
    expect($location->account_id)->toBe($user->account_id);
});

it('validates the name when creating a location', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/locations', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('rejects a parent location from another account', function () {
    $user = $this->createUser();
    $foreignLocation = Location::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/locations', [
        'name' => 'Box 1',
        'parent_id' => $foreignLocation->id,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['parent_id']);
});

it('restricts location creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/locations', [
        'name' => 'Box 1',
    ]);

    $response->assertNotFound();
});

it('updates a location', function () {
    Queue::fake();

    $user = $this->createUser();
    $location = Location::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Shelf A',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/locations/'.$location->id, [
        'name' => 'Shelf B',
        'emoji' => '🏠',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Shelf B')
        ->assertJsonPath('data.attributes.emoji', '🏠');

    expect($location->refresh()->name)->toBe('Shelf B');
});

it('restricts location updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $location = Location::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/locations/'.$location->id, [
        'name' => 'Shelf B',
    ]);

    $response->assertNotFound();
});

it('deletes a location and its children', function () {
    Queue::fake();

    $user = $this->createUser();
    $location = Location::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $child = Location::factory()->create([
        'account_id' => $user->account_id,
        'parent_id' => $location->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/locations/'.$location->id);

    $response->assertNoContent();

    $this->assertModelMissing($location);
    $this->assertModelMissing($child);
});

it('restricts location deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $location = Location::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/locations/'.$location->id);

    $response->assertNotFound();
});
