<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
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
            'position',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('lists the groups of a type in position order', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Second', 'position' => 2]);
    CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'First', 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/custom-field-groups')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonStructure([
            'data' => ['*' => $this->jsonStructure],
            'links',
            'meta',
        ])
        ->assertJsonPath('data.0.type', 'custom_field_group')
        ->assertJsonPath('data.0.attributes.name', 'First')
        ->assertJsonPath('data.1.attributes.name', 'Second');
});

it('does not list the groups of another accounts type', function () {
    $user = $this->createUser();
    $foreignType = CollectionType::factory()->create();
    CustomFieldGroup::factory()->create(['type_id' => $foreignType->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$foreignType->id.'/custom-field-groups')->assertNotFound();
});

it('shows a group', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Publishing info']);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id)
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.id', (string) $group->id)
        ->assertJsonPath('data.attributes.name', 'Publishing info');
});

it('returns not found for a group that belongs to another type', function () {
    $user = $this->createUser();
    $comics = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $wine = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $wine->id]);

    Sanctum::actingAs($user);

    $this->json('GET', '/api/collection-types/'.$comics->id.'/custom-field-groups/'.$group->id)->assertNotFound();
});

it('creates a group', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/'.$type->id.'/custom-field-groups', [
        'name' => 'Publishing info',
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.attributes.name', 'Publishing info')
        ->assertJsonPath('data.attributes.position', 1);

    expect($type->customFieldGroups()->count())->toBe(1);
});

it('validates the name when creating a group', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/'.$type->id.'/custom-field-groups', [
        'name' => str_repeat('a', 256),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('restricts group creation to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/collection-types/'.$type->id.'/custom-field-groups', [
        'name' => 'Publishing info',
    ])->assertNotFound();

    expect($type->customFieldGroups()->count())->toBe(0);
});

it('updates a group', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main']);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id, [
        'name' => 'Publishing info',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.name', 'Publishing info');

    expect($group->refresh()->name)->toBe('Publishing info');
});

it('restricts group updates to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main']);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id, [
        'name' => 'Publishing info',
    ])->assertNotFound();

    expect($group->refresh()->name)->toBe('Main');
});

it('deletes a group and keeps its fields as standalone', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'group_id' => $group->id]);

    Sanctum::actingAs($user);

    $this->json('DELETE', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id)
        ->assertNoContent();

    $this->assertModelMissing($group);
    $this->assertModelExists($field);
    expect($field->refresh()->group_id)->toBeNull();
});

it('restricts group deletion to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id]);

    Sanctum::actingAs($viewer);

    $this->json('DELETE', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id)->assertNotFound();
    $this->assertModelExists($group);
});
