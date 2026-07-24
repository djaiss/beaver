<?php

declare(strict_types=1);
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'schema' => [
                'schemaVersion',
                'type' => [
                    'name',
                    'color',
                    'groups',
                    'standaloneFields',
                ],
            ],
        ],
        'links' => [
            'self',
            'collection_type',
        ],
    ];
});

it('exports the schema of a collection type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create([
        'account_id' => $user->account_id,
        'name' => 'Comics',
        'color' => '#fb923c',
    ]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info', 'position' => 1]);
    CustomField::factory()->create([
        'type_id' => $type->id,
        'group_id' => $group->id,
        'name' => 'Publisher',
        'field_type' => FieldTypeEnum::Select->value,
        'options' => ['Marvel', 'DC'],
        'position' => 1,
    ]);
    CustomField::factory()->create([
        'type_id' => $type->id,
        'group_id' => null,
        'name' => 'Signed',
        'field_type' => FieldTypeEnum::Boolean->value,
        'position' => 1,
    ]);

    Sanctum::actingAs($user);

    $response = $this->json('GET', '/api/collection-types/'.$type->id.'/export');

    $response->assertOk();
    $response->assertJsonStructure(['data' => $this->jsonStructure]);
    $response->assertJsonPath('data.type', 'collection_type_export');
    $response->assertJsonPath('data.id', (string) $type->id);
    $response->assertJsonPath('data.attributes.schema.schemaVersion', 1);
    $response->assertJsonPath('data.attributes.schema.type.name', 'Comics');
    $response->assertJsonPath('data.attributes.schema.type.color', '#fb923c');
    $response->assertJsonPath('data.attributes.schema.type.groups.0.name', 'Publishing info');
    $response->assertJsonPath('data.attributes.schema.type.groups.0.fields.0.name', 'Publisher');
    $response->assertJsonPath('data.attributes.schema.type.groups.0.fields.0.type', 'select');
    $response->assertJsonPath('data.attributes.schema.type.groups.0.fields.0.options', ['Marvel', 'DC']);
    $response->assertJsonPath('data.attributes.schema.type.standaloneFields.0.name', 'Signed');
    $response->assertJsonPath('data.attributes.schema.type.standaloneFields.0.type', 'boolean');
});

it('links back to itself and to the collection type', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Comics']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')
        ->assertOk()
        ->assertJsonPath('data.links.self', route('api.collectionTypes.export.show', $type->id))
        ->assertJsonPath('data.links.collection_type', route('api.collectionTypes.show', $type->id));
});

it('exports a type that has no group and no field', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id, 'name' => 'Vinyl']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')
        ->assertOk()
        ->assertJsonCount(0, 'data.attributes.schema.type.groups')
        ->assertJsonCount(0, 'data.attributes.schema.type.standaloneFields');
});

it('does not export a type of another account', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['name' => 'Foreign type']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')->assertNotFound();
});

it('restricts the export to owners and editors', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);

    Sanctum::actingAs($viewer);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')->assertNotFound();
});

it('allows an editor to export a type', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);

    Sanctum::actingAs($editor);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')
        ->assertOk()
        ->assertJsonPath('data.attributes.schema.type.name', 'Comics');
});

it('rejects an unauthenticated request', function () {
    $type = CollectionType::factory()->create(['name' => 'Comics']);

    $this->json('GET', '/api/collection-types/'.$type->id.'/export')->assertUnauthorized();
});
