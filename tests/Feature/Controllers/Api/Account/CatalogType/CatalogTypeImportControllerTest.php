<?php

declare(strict_types=1);
use App\Actions\ImportCatalogType;
use App\Enums\PermissionEnum;
use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

/**
 * A valid export document, as ExportCatalogType produces it.
 */
function importTestDocument(array $overrides = []): string
{
    return (string) json_encode(array_merge([
        'schemaVersion' => 1,
        'type' => [
            'name' => 'Comics',
            'color' => '#6B7280',
            'groups' => [
                [
                    'name' => 'Main',
                    'fields' => [
                        ['name' => 'Issue #', 'type' => 'text'],
                        ['name' => 'Grade', 'type' => 'select', 'options' => ['Mint', 'Good']],
                    ],
                ],
            ],
            'standaloneFields' => [
                ['name' => 'Publisher', 'type' => 'text'],
            ],
        ],
    ], $overrides));
}

beforeEach(function () {
    $this->jsonStructure = [
        'type',
        'id',
        'attributes' => [
            'name',
            'color',
            'created_at',
            'updated_at',
        ],
        'links' => [
            'self',
        ],
    ];
});

it('imports a collection type', function () {
    Queue::fake();

    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/import', [
        'json' => importTestDocument(),
    ])
        ->assertCreated()
        ->assertJsonStructure(['data' => $this->jsonStructure])
        ->assertJsonPath('data.type', 'collection_type')
        ->assertJsonPath('data.attributes.name', 'Comics')
        ->assertJsonPath('data.attributes.color', '#6B7280');

    $type = CatalogType::query()->latest('id')->first();
    expect($type->name)->toBe('Comics');
    expect($type->account_id)->toBe($user->account_id);
    expect(CustomFieldGroup::query()->count())->toBe(1);
    expect(CustomField::query()->count())->toBe(3);
});

it('validates the json when importing a collection type', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/import', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['json']);
});

it('rejects a document larger than the maximum length', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/import', [
        'json' => str_repeat('a', ImportCatalogType::MAX_LENGTH + 1),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['json']);
});

it('rejects a document that is not valid json', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/import', [
        'json' => 'not json at all',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['json']);
});

it('rejects a document with an unsupported schema version', function () {
    $user = $this->createUser();

    Sanctum::actingAs($user);

    $this->json('POST', '/api/collection-types/import', [
        'json' => importTestDocument(['schemaVersion' => 99]),
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['json']);

    expect(CatalogType::query()->count())->toBe(0);
});

it('restricts importing a collection type to owners and editors', function () {
    Queue::fake();

    $account = $this->createAccount();
    $viewer = $this->assignUserToAccount($this->createUser(), $account, PermissionEnum::Viewer->value);

    Sanctum::actingAs($viewer);

    $this->json('POST', '/api/collection-types/import', [
        'json' => importTestDocument(),
    ])
        ->assertNotFound();

    expect(CatalogType::query()->count())->toBe(0);
});
