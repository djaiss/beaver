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
            'group_id',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('moves a custom field up', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Issue #', 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Grade', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$second->id.'/order', [
        'direction' => 'up',
    ])
        ->assertOk()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'custom_field')
        ->assertJsonPath('data.id', (string) $second->id)
        ->assertJsonPath('data.attributes.position', 1);

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('moves a custom field down', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Issue #', 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Grade', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$first->id.'/order', [
        'direction' => 'down',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.position', 2);

    expect($first->refresh()->position)->toBe(2);
    expect($second->refresh()->position)->toBe(1);
});

it('leaves the first custom field alone when moving it up', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Issue #', 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'name' => 'Grade', 'position' => 2]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$first->id.'/order', [
        'direction' => 'up',
    ])
        ->assertOk()
        ->assertJsonPath('data.attributes.position', 1);

    expect($second->refresh()->position)->toBe(2);
});

it('validates the direction when moving a custom field', function () {
    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id.'/order', [
        'direction' => 'sideways',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['direction']);
});

it('returns not found when moving a custom field of another account', function () {
    Queue::fake();

    $user = $this->createUser();
    $otherAccount = $this->createAccount('Moondance Diner');
    $type = CollectionType::factory()->create(['account_id' => $otherAccount->id]);
    $field = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id.'/order', [
        'direction' => 'up',
    ])
        ->assertNotFound();
});

it('returns not found when the custom field belongs to another type', function () {
    Queue::fake();

    $user = $this->createUser();
    $type = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $otherType = CollectionType::factory()->create(['account_id' => $user->account_id]);
    $field = CustomField::factory()->create(['type_id' => $otherType->id, 'position' => 1]);

    Sanctum::actingAs($user);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$field->id.'/order', [
        'direction' => 'up',
    ])
        ->assertNotFound();
});

it('restricts moving a custom field to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $first = CustomField::factory()->create(['type_id' => $type->id, 'position' => 1]);
    $second = CustomField::factory()->create(['type_id' => $type->id, 'position' => 2]);

    Sanctum::actingAs($viewer);

    $this->json('PUT', '/api/collection-types/'.$type->id.'/custom-fields/'.$second->id.'/order', [
        'direction' => 'up',
    ])
        ->assertNotFound();

    expect($first->refresh()->position)->toBe(1);
    expect($second->refresh()->position)->toBe(2);
});
