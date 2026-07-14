<?php

declare(strict_types=1);
use App\Actions\UpdateCollectionType;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates a type and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id, 'name' => 'Old name', 'color' => '#111111']);

    $result = new UpdateCollectionType(
        user: $editor,
        collectionType: $collectionType,
        name: 'Comics',
        color: '#1D4ED8',
    )->execute();

    expect($result)->toBeInstanceOf(CollectionType::class);
    expect($collectionType->fresh()->name)->toBe('Comics');
    expect($collectionType->fresh()->color)->toBe('#1D4ED8');
    expect($collectionType->fresh()->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionTypeUpdate,
    );
});

it('throws when the color is not a valid hex', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new UpdateCollectionType(
        user: $owner,
        collectionType: $collectionType,
        name: 'Comics',
        color: 'not-a-color',
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);

    new UpdateCollectionType(
        user: $viewer,
        collectionType: $collectionType,
        name: 'Comics',
    )->execute();
});
