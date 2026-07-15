<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\Condition;
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
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the conditions of the account', function () {
    $user = $this->createUser();
    Condition::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'New',
    ]);
    Condition::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Used',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/conditions');

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
        ->assertJsonPath('data.0.attributes.name', 'New')
        ->assertJsonPath('data.1.attributes.name', 'Used');
});

it('does not list conditions from another account', function () {
    $user = $this->createUser();
    Condition::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/conditions');

    $response
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('shows a condition', function () {
    $user = $this->createUser();
    $condition = Condition::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'New',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/conditions/'.$condition->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'condition')
        ->assertJsonPath('data.id', (string) $condition->id)
        ->assertJsonPath('data.attributes.name', 'New')
        ->assertJsonPath('data.links.self', route('api.conditions.show', $condition->id));
});

it('returns not found for a condition from another account', function () {
    $user = $this->createUser();
    $condition = Condition::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/conditions/'.$condition->id);

    $response->assertNotFound();
});

it('creates a condition', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/conditions', [
        'name' => 'New',
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'New');

    $condition = Condition::query()->latest('id')->first();
    expect($condition->name)->toBe('New');
    expect($condition->account_id)->toBe($user->account_id);
});

it('validates the name when creating a condition', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/conditions', []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

it('restricts condition creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/conditions', [
        'name' => 'New',
    ]);

    $response->assertNotFound();
});

it('updates a condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $condition = Condition::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Old name',
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/conditions/'.$condition->id, [
        'name' => 'Used',
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Used');

    expect($condition->refresh()->name)->toBe('Used');
});

it('restricts condition updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $condition = Condition::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/conditions/'.$condition->id, [
        'name' => 'Used',
    ]);

    $response->assertNotFound();
});

it('deletes a condition', function () {
    Queue::fake();

    $user = $this->createUser();
    $condition = Condition::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/conditions/'.$condition->id);

    $response->assertNoContent();

    $this->assertModelMissing($condition);
});

it('restricts condition deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $condition = Condition::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/conditions/'.$condition->id);

    $response->assertNotFound();
});
