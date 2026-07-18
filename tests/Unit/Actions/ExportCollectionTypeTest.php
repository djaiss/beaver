<?php

declare(strict_types=1);
use App\Actions\ExportCollectionType;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('exports the groups and the fields of a type', function () {
    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $collectionType = CollectionType::factory()->create([
        'account_id' => $account->id,
        'name' => 'Comics',
        'color' => '#fb923c',
    ]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id, 'name' => 'Publishing info', 'position' => 1]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'group_id' => $group->id, 'name' => 'Publisher', 'field_type' => FieldTypeEnum::Text->value, 'position' => 1]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'group_id' => null, 'name' => 'Signed', 'field_type' => FieldTypeEnum::Boolean->value, 'position' => 1]);

    $schema = new ExportCollectionType(
        user: $editor,
        collectionType: $collectionType,
    )->execute();

    expect($schema)->toBe([
        'schemaVersion' => ExportCollectionType::SCHEMA_VERSION,
        'type' => [
            'name' => 'Comics',
            'color' => '#fb923c',
            'groups' => [
                [
                    'name' => 'Publishing info',
                    'fields' => [
                        ['name' => 'Publisher', 'type' => 'text'],
                    ],
                ],
            ],
            'standaloneFields' => [
                ['name' => 'Signed', 'type' => 'boolean'],
            ],
        ],
    ]);
});

it('exports the options of a select field', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collectionType = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Comics']);
    CustomField::factory()->create([
        'type_id' => $collectionType->id,
        'name' => 'Grade',
        'field_type' => FieldTypeEnum::Select->value,
        'options' => ['CGC 9.8', 'CGC 9.6', 'Raw'],
    ]);

    $schema = new ExportCollectionType(user: $owner, collectionType: $collectionType)->execute();

    expect($schema['type']['standaloneFields'])->toBe([
        ['name' => 'Grade', 'type' => 'select', 'options' => ['CGC 9.8', 'CGC 9.6', 'Raw']],
    ]);
});

it('omits the options key when a field has none', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'name' => 'Rachel', 'options' => []]);

    $schema = new ExportCollectionType(user: $owner, collectionType: $collectionType)->execute();

    expect($schema['type']['standaloneFields'][0])->not->toHaveKey('options');
});

it('orders groups and fields by their position', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $second = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id, 'name' => 'Chandler', 'position' => 2]);
    $first = CustomFieldGroup::factory()->create(['type_id' => $collectionType->id, 'name' => 'Monica', 'position' => 1]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'group_id' => $first->id, 'name' => 'Joey', 'position' => 2]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'group_id' => $first->id, 'name' => 'Ross', 'position' => 1]);
    CustomField::factory()->create(['type_id' => $collectionType->id, 'group_id' => $second->id, 'name' => 'Phoebe', 'position' => 1]);

    $schema = new ExportCollectionType(user: $owner, collectionType: $collectionType)->execute();

    expect(array_column($schema['type']['groups'], 'name'))->toBe(['Monica', 'Chandler']);
    expect(array_column($schema['type']['groups'][0]['fields'], 'name'))->toBe(['Ross', 'Joey']);
});

it('exports a type that has no group and no field', function () {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collectionType = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Vinyl']);

    $schema = new ExportCollectionType(user: $owner, collectionType: $collectionType)->execute();

    expect($schema['type']['groups'])->toBe([]);
    expect($schema['type']['standaloneFields'])->toBe([]);
});

it('throws when the user may not manage the account', function () {
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new ExportCollectionType(user: $viewer, collectionType: $collectionType)->execute();
});
