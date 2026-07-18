<?php

declare(strict_types=1);
use App\Actions\ExportCollectionType;
use App\Actions\ImportCollectionType;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

/**
 * A well formed document, in the exact shape ExportCollectionType hands out.
 * Pass an array of overrides to break one part of it.
 */
function schemaJson(array $type = [], array $document = []): string
{
    return json_encode(array_merge([
        'schemaVersion' => 1,
        'type' => array_merge([
            'name' => 'Comics',
            'color' => '#FB923C',
            'groups' => [
                [
                    'name' => 'Publishing info',
                    'fields' => [
                        ['name' => 'Issue #', 'type' => 'number'],
                        ['name' => 'Grade', 'type' => 'select', 'options' => ['CGC 9.8', 'Raw']],
                    ],
                ],
            ],
            'standaloneFields' => [
                ['name' => 'Notes', 'type' => 'text'],
            ],
        ], $type),
    ], $document));
}

it('imports a type with its groups, fields and options', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $type = new ImportCollectionType(
        user: $editor,
        account: $account,
        json: schemaJson(),
    )->execute();

    expect($type)->toBeInstanceOf(CollectionType::class);
    expect($type->name)->toBe('Comics');
    expect($type->color)->toBe('#FB923C');
    expect($type->account_id)->toBe($account->id);

    $group = $type->customFieldGroups()->sole();
    expect($group->name)->toBe('Publishing info');

    $grouped = $group->customFields()->orderBy('position')->get();
    expect($grouped)->toHaveCount(2);
    expect($grouped[0]->name)->toBe('Issue #');
    expect($grouped[0]->field_type)->toBe(FieldTypeEnum::Number);
    expect($grouped[1]->name)->toBe('Grade');
    expect($grouped[1]->field_type)->toBe(FieldTypeEnum::Select);
    expect($grouped[1]->options)->toBe(['CGC 9.8', 'Raw']);

    $standalone = $type->ungroupedCustomFields()->sole();
    expect($standalone->name)->toBe('Notes');
    expect($standalone->field_type)->toBe(FieldTypeEnum::Text);

    expect($type->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionTypeCreation,
    );
});

it('imports a type that has no groups and no fields at all', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = new ImportCollectionType(
        user: $owner,
        account: $account,
        json: schemaJson(['groups' => [], 'standaloneFields' => []]),
    )->execute();

    expect($type->customFieldGroups()->count())->toBe(0);
    expect($type->customFields()->count())->toBe(0);
});

it('falls back to the default color when the document has none', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = new ImportCollectionType(
        user: $owner,
        account: $account,
        json: json_encode(['schemaVersion' => 1, 'type' => ['name' => 'Vinyl', 'groups' => []]]),
    )->execute();

    expect($type->color)->toBe('#6B7280');
});

it('sanitizes the names it imports', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = new ImportCollectionType(
        user: $owner,
        account: $account,
        json: schemaJson([
            'name' => '<script>alert(1)</script>Vinyl',
            'groups' => [['name' => '<strong>Pressing</strong>', 'fields' => [['name' => '<em>Label</em>', 'type' => 'text']]]],
            'standaloneFields' => [],
        ]),
    )->execute();

    expect($type->name)->toBe('Vinyl');

    $group = $type->customFieldGroups()->sole();
    expect($group->name)->toBe('Pressing');
    expect($group->customFields()->sole()->name)->toBe('Label');
});

it('does not let a viewer import a type', function (): void {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $this->expectException(ModelNotFoundException::class);

    new ImportCollectionType(user: $viewer, account: $account, json: schemaJson())->execute();
});

it('does not let someone import into an account they do not belong to', function (): void {
    $account = $this->createAccount();
    $stranger = $this->createUser();

    $this->expectException(ModelNotFoundException::class);

    new ImportCollectionType(user: $stranger, account: $account, json: schemaJson())->execute();
});

/**
 * Every one of these documents is rejected, and nothing is written.
 */
dataset('invalid documents', [
    'empty input' => [''],
    'not JSON at all' => ['this is not json'],
    'a JSON array at the root' => ['[1, 2, 3]'],
    'a JSON scalar at the root' => ['"Comics"'],
    'a document that is too large' => ['{"padding": "'.str_repeat('a', 100_001).'"}'],
    'nesting deeper than we parse' => [str_repeat('[', 40).str_repeat(']', 40)],
    'a missing schema version' => ['{"type": {"name": "Comics", "groups": []}}'],
    'a schema version we do not support' => ['{"schemaVersion": 99, "type": {"name": "Comics", "groups": []}}'],
    'a schema version that is not a number' => ['{"schemaVersion": "1", "type": {"name": "Comics", "groups": []}}'],
    'a missing type' => ['{"schemaVersion": 1}'],
    'a type that is not an object' => ['{"schemaVersion": 1, "type": []}'],
    'a type without a name' => ['{"schemaVersion": 1, "type": {"groups": []}}'],
    'a type whose name is blank' => ['{"schemaVersion": 1, "type": {"name": "   ", "groups": []}}'],
    'a type whose name is not a string' => ['{"schemaVersion": 1, "type": {"name": 42, "groups": []}}'],
    'a malformed color' => ['{"schemaVersion": 1, "type": {"name": "Comics", "color": "red", "groups": []}}'],
    'groups that are not an array' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": {"a": 1}}}'],
    'a group without a name' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"fields": []}]}}'],
    'fields that are not an array' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": "nope"}]}}'],
    'a field without a name' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": [{"type": "text"}]}]}}'],
    'an unknown field type' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": [{"name": "X", "type": "rce"}]}]}}'],
    'a select field without options' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": [{"name": "X", "type": "select"}]}]}}'],
    'a select field with empty options' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": [{"name": "X", "type": "select", "options": []}]}]}}'],
    'a select field whose options are not strings' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [{"name": "Main", "fields": [{"name": "X", "type": "select", "options": [{"a": 1}]}]}]}}'],
    'a standalone field with an unknown type' => ['{"schemaVersion": 1, "type": {"name": "Comics", "groups": [], "standaloneFields": [{"name": "X", "type": "nope"}]}}'],
]);

it('rejects a document it cannot trust and writes nothing', function (string $json): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    expect(fn (): CollectionType => new ImportCollectionType(user: $owner, account: $account, json: $json)->execute())
        ->toThrow(ValidationException::class);

    expect($account->collectionTypes()->count())->toBe(0);
})->with('invalid documents');

it('reports every problem in the document at once', function (): void {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $json = json_encode([
        'schemaVersion' => 1,
        'type' => [
            'name' => 'Comics',
            'groups' => [
                ['name' => 'Main', 'fields' => [['name' => 'A', 'type' => 'nope'], ['type' => 'text']]],
            ],
        ],
    ]);

    try {
        new ImportCollectionType(user: $owner, account: $account, json: $json)->execute();
    } catch (ValidationException $exception) {
        // The unknown type on the first field, and the missing name on the second.
        expect($exception->errors()['json'])->toHaveCount(2);

        return;
    }

    $this->fail('The import should have been rejected.');
});

it('refuses a document with more fields than we allow', function (): void {
    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $fields = array_map(fn (int $index): array => ['name' => 'Field '.$index, 'type' => 'text'], range(1, 400));

    $json = json_encode(['schemaVersion' => 1, 'type' => ['name' => 'Comics', 'groups' => [['name' => 'Main', 'fields' => $fields]]]]);

    expect(fn (): CollectionType => new ImportCollectionType(user: $owner, account: $account, json: $json)->execute())
        ->toThrow(ValidationException::class);

    expect($account->collectionTypes()->count())->toBe(0);
});

it('drops options hung off a field that is not a select', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = new ImportCollectionType(
        user: $owner,
        account: $account,
        json: json_encode(['schemaVersion' => 1, 'type' => [
            'name' => 'Comics',
            'groups' => [],
            'standaloneFields' => [['name' => 'Notes', 'type' => 'text', 'options' => ['a', 'b']]],
        ]]),
    )->execute();

    expect($type->ungroupedCustomFields()->sole()->options)->toBeNull();
});

it('imports a document exported by the export action', function (): void {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $source = new ImportCollectionType(user: $owner, account: $account, json: schemaJson())->execute();

    $exported = new ExportCollectionType(user: $owner, collectionType: $source)->execute();

    $imported = new ImportCollectionType(
        user: $owner,
        account: $account,
        json: json_encode($exported),
    )->execute();

    expect($imported->id)->not->toBe($source->id);
    expect(new ExportCollectionType(user: $owner, collectionType: $imported)->execute())->toBe($exported);
});
