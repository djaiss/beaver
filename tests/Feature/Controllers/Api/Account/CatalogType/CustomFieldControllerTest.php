<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
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
            'group_id',
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
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields');

    $response->assertNotFound();
});

it('shows a custom field', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
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
        ->assertJsonPath('data.links.self', route('api.catalogTypes.customFields.show', [
            'collectionType' => $type->id,
            'customField' => $field->id,
        ]));
});

it('returns not found for a field that belongs to another type', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);
    $otherType = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
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

it('creates a rating custom field', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
        'account_id' => $user->account_id,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types/'.$type->id.'/custom-fields', [
        'name' => 'My Rating',
        'field_type' => 'rating',
    ]);

    $response->assertCreated();
    $response->assertJsonPath('data.attributes.field_type', 'rating');
});

it('validates the field type when creating a custom field', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
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
    $type = CatalogType::factory()->create([
        'account_id' => $account->id,
    ]);
    $field = CustomField::factory()->create([
        'type_id' => $type->id,
    ]);

    Sanctum::actingAs($viewer);

    $response = $this->json('DELETE', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id);

    $response->assertNotFound();
});

it('creates a field inside a group', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    Sanctum::actingAs($user);

    $response = $this->json('POST', '/api/collection-types/'.$type->id.'/custom-fields', [
        'name' => 'Grade',
        'field_type' => 'text',
        'group_id' => $group->id,
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('data.attributes.group_id', (string) $group->id);

    expect($type->customFields()->first()->group_id)->toBe($group->id);
});

it('exposes a null group_id for a standalone field', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => null]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id)
        ->assertOk()
        ->assertJsonPath('data.attributes.group_id', null);
});

it('refuses a group belonging to another type', function () {
    Queue::fake();

    $user = $this->createUser();
    $comics = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $wine = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $foreignGroup = CustomFieldGroup::factory()->create(['type_id' => $wine->id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/'.$comics->id.'/custom-fields', [
        'name' => 'Grade',
        'field_type' => 'text',
        'group_id' => $foreignGroup->id,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('group_id');
});
