<?php

declare(strict_types=1);
use App\Actions\UpdateCustomField;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates a custom field including its position', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Chandler', 'last_name' => 'Bing']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $customField = CustomField::factory()->create([
        'type_id' => $collectionType->id,
        'name' => 'Old name',
        'field_type' => FieldTypeEnum::Text->value,
        'position' => 1,
    ]);

    $result = new UpdateCustomField(
        user: $editor,
        customField: $customField,
        name: 'Grade',
        fieldType: FieldTypeEnum::Select->value,
        options: ['Mint', 'Good'],
        position: 3,
    )->execute();

    expect($result)->toBeInstanceOf(CustomField::class);
    expect($customField->fresh()->name)->toBe('Grade');
    expect($customField->fresh()->field_type)->toBe(FieldTypeEnum::Select);
    expect($customField->fresh()->options)->toBe(['Mint', 'Good']);
    expect($customField->fresh()->position)->toBe(3);
    expect($customField->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldUpdate,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $customField = CustomField::factory()->create(['type_id' => $collectionType->id]);

    new UpdateCustomField(
        user: $viewer,
        customField: $customField,
        name: 'Grade',
    )->execute();
});
