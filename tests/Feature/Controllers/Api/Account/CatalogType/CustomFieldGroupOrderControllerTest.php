<?php

declare(strict_types=1);
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
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

it('moves a custom field group up', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main', 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Details', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$second->id.'/order', [
        'direction' => 'up',
    ])
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'custom_field_group')
        ->assertJsonPath('data.id', (string) $second->id)
        ->assertJsonPath('data.attributes.position', 1);

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('moves a custom field group down', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main', 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Details', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$first->id.'/order', [
        'direction' => 'down',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.position', 2);

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('leaves the last custom field group alone when moving it down', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Main', 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'name' => 'Details', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$second->id.'/order', [
        'direction' => 'down',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.position', 2);

    expect($first->refresh()->position)->toBe(1);
});

it('validates the direction when moving a custom field group', function () {
    $user = $this->createUser();
    $type = CatalogType::factory()->create(['account_id' => $user->account_id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id.'/order', [
        'direction' => 'sideways',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['direction']);
});

it('returns not found when moving a custom field group of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $type = CatalogType::factory()->create(['account_id' => $otherAccount->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$group->id.'/order', [
        'direction' => 'up',
    ])
        ->assertNotFound();
});

it('restricts moving a custom field group to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CatalogType::factory()->create(['account_id' => $account->id]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $type->id, 'position' => 2]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-field-groups/'.$second->id.'/order', [
        'direction' => 'up',
    ])
        ->assertNotFound();

    expect($first->refresh()->position)->toBe(1);
    expect($second->refresh()->position)->toBe(2);
});
