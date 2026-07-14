<?php

declare(strict_types=1);
use App\Actions\DestroyCustomField;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a custom field', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $customField = CustomField::factory()->create(['type_id' => $collectionType->id]);

    new DestroyCustomField(
        user: $owner,
        customField: $customField,
    )->execute();

    $this->assertDatabaseMissing('custom_fields', ['id' => $customField->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CustomFieldDeletion,
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

    new DestroyCustomField(
        user: $viewer,
        customField: $customField,
    )->execute();
});
