<?php

declare(strict_types=1);
use App\Actions\CreateCustomField;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\CustomField;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a custom field and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $customField = new CreateCustomField(
        user: $editor,
        catalogType: $catalogType,
        name: 'Grade',
        fieldType: FieldTypeEnum::Select->value,
        options: ['Mint', 'Near Mint'],
    )->execute();

    expect($customField)->toBeInstanceOf(CustomField::class);
    expect($customField->name)->toBe('Grade');
    expect($customField->field_type)->toBe(FieldTypeEnum::Select);
    expect($customField->options)->toBe(['Mint', 'Near Mint']);
    expect($customField->type_id)->toBe($catalogType->id);
    expect($customField->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldCreation,
    );
});

it('auto-increments the position within the collection type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    $first = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Issue #')->execute();
    $second = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Publisher')->execute();

    expect($first->position)->toBe(1);
    expect($second->position)->toBe(2);
});

it('throws when the field type is invalid', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new CreateCustomField(
        user: $owner,
        catalogType: $catalogType,
        name: 'Grade',
        fieldType: 'hologram',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);

    new CreateCustomField(
        user: $viewer,
        catalogType: $catalogType,
        name: 'Grade',
    )->execute();
});

it('creates a field inside a group', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    $customField = new CreateCustomField(
        user: $owner,
        catalogType: $catalogType,
        name: 'Grade',
        group: $group,
    )->execute();

    expect($customField->group_id)->toBe($group->id);
    expect($customField->type_id)->toBe($catalogType->id);
});

it('counts the positions of each group separately from the standalone fields', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalogType = CatalogType::factory()->create(['account_id' => $account->id]);
    $group = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);
    $other = CustomFieldGroup::factory()->create(['type_id' => $catalogType->id]);

    $standalone = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Notes')->execute();
    $firstOfGroup = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Issue #', group: $group)->execute();
    $secondOfGroup = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Publisher', group: $group)->execute();
    $firstOfOther = new CreateCustomField(user: $owner, catalogType: $catalogType, name: 'Grade', group: $other)->execute();

    // A position orders a field within its group, so every list restarts at 1.
    expect($standalone->position)->toBe(1);
    expect($firstOfGroup->position)->toBe(1);
    expect($secondOfGroup->position)->toBe(2);
    expect($firstOfOther->position)->toBe(1);
});

it('throws when the group belongs to another type', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $comics = CatalogType::factory()->create(['account_id' => $account->id]);
    $wine = CatalogType::factory()->create(['account_id' => $account->id]);
    $foreignGroup = CustomFieldGroup::factory()->create(['type_id' => $wine->id]);

    new CreateCustomField(
        user: $owner,
        catalogType: $comics,
        name: 'Grade',
        group: $foreignGroup,
    )->execute();
});
