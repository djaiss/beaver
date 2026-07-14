<?php

declare(strict_types=1);
use App\Actions\CreateCustomField;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomField;
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
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    $customField = new CreateCustomField(
        user: $editor,
        collectionType: $collectionType,
        name: 'Grade',
        fieldType: FieldTypeEnum::Select->value,
        options: ['Mint', 'Near Mint'],
    )->execute();

    expect($customField)->toBeInstanceOf(CustomField::class);
    expect($customField->name)->toBe('Grade');
    expect($customField->field_type)->toBe(FieldTypeEnum::Select);
    expect($customField->options)->toBe(['Mint', 'Near Mint']);
    expect($customField->type_id)->toBe($collectionType->id);
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
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    $first = new CreateCustomField(user: $owner, collectionType: $collectionType, name: 'Issue #')->execute();
    $second = new CreateCustomField(user: $owner, collectionType: $collectionType, name: 'Publisher')->execute();

    expect($first->position)->toBe(1);
    expect($second->position)->toBe(2);
});

it('throws when the field type is invalid', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new CreateCustomField(
        user: $owner,
        collectionType: $collectionType,
        name: 'Grade',
        fieldType: 'rating',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new CreateCustomField(
        user: $viewer,
        collectionType: $collectionType,
        name: 'Grade',
    )->execute();
});
