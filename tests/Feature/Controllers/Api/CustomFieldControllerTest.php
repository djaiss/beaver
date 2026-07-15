<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
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
            'field_type',
            'options',
            'position',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the custom fields of a type in position order', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    CustomField::factory()->create([
        'type_id' => $type->id,
        'name' => 'Second',
        'position' => 2,
    ]);
    CustomField::factory()->create([
        'type_id' => $type->id,
        'name' => 'First',
        'position' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields');

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
        ->assertJsonPath('data.0.attributes.name', 'First')
        ->assertJsonPath('data.1.attributes.name', 'Second');
});

it('returns not found when listing fields of a type from another account', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields');

    $response->assertNotFound();
});

it('shows a custom field', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
        'name' => 'Issue #',
        'field_type' => 'number',
        'position' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.type', 'custom_field')
        ->assertJsonPath('data.id', (string) $field->id)
        ->assertJsonPath('data.attributes.name', 'Issue #')
        ->assertJsonPath('data.attributes.field_type', 'number')
        ->assertJsonPath('data.attributes.position', 1)
        ->assertJsonPath('data.links.self', route('api.collectionTypes.customFields.show', [
            'collectionType' => $type->id,
            'customField' => $field->id,
        ]));
});

it('returns not found for a field that belongs to another type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $otherType = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $otherType->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id);

    $response->assertNotFound();
});

it('creates a custom field', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types/'.$type->id.'/custom-fields', [
        'name' => 'Grade',
        'field_type' => 'select',
        'options' => ['NM', '', 'VF'],
    ]);

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Grade')
        ->assertJsonPath('data.attributes.field_type', 'select')
        ->assertJsonPath('data.attributes.options', ['NM', 'VF'])
        ->assertJsonPath('data.attributes.position', 1);

    $field = CustomField::query()->first();
    expect($field->name)->toBe('Grade');
    expect($field->type_id)->toBe($type->id);
});

it('validates the field type when creating a custom field', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types/'.$type->id.'/custom-fields', [
        'name' => 'Grade',
        'field_type' => 'invalid',
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['field_type']);
});

it('restricts custom field creation to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create([
        'account_id' => $account->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('POST', '/api/collection-types/'.$type->id.'/custom-fields', [
        'name' => 'Grade',
        'field_type' => 'text',
    ]);

    $response->assertNotFound();
});

it('updates a custom field', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
        'name' => 'Issue #',
        'position' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id, [
        'name' => 'Vintage',
        'field_type' => 'date',
        'position' => 3,
    ]);

    $response
        ->assertOk()
        ->assertJsonStructure([
            'data' => $this->jsonStructure,
        ])
        ->assertJsonPath('data.attributes.name', 'Vintage')
        ->assertJsonPath('data.attributes.field_type', 'date')
        ->assertJsonPath('data.attributes.position', 3);

    expect($field->refresh()->name)->toBe('Vintage');
});

it('restricts custom field updates to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create([
        'account_id' => $account->id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id, [
        'name' => 'Vintage',
        'field_type' => 'text',
    ]);

    $response->assertNotFound();
});

it('deletes a custom field', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('DELETE', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id);

    $response->assertNoContent();

    $this->assertModelMissing($field);
});

it('restricts custom field deletion to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create([
        'account_id' => $account->id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id);

    $response->assertNotFound();
});
