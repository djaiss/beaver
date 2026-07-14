<?php

declare(strict_types=1);
use App\Actions\DestroyCollectionType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new DestroyCollectionType(
        user: $owner,
        collectionType: $collectionType,
    )->execute();

    $this->assertDatabaseMissing('types', ['id' => $collectionType->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionTypeDeletion,
    );
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new DestroyCollectionType(
        user: $viewer,
        collectionType: $collectionType,
    )->execute();
});
