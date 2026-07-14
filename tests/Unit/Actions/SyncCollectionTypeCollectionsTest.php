<?php

declare(strict_types=1);
use App\Actions\SyncCollectionTypeCollections;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('syncs the collections a type is linked to', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $comics = Collection::factory()->create(['account_id' => $account->id]);
    $vinyl = Collection::factory()->create(['account_id' => $account->id]);

    new SyncCollectionTypeCollections(user: $editor, collectionType: $type, collectionIds: [$comics->id, $vinyl->id])->execute();

    expect($type->collections()->pluck('collections.id')->all())->toEqualCanonicalizing([$comics->id, $vinyl->id]);

    new SyncCollectionTypeCollections(user: $editor, collectionType: $type, collectionIds: [$comics->id])->execute();

    expect($type->collections()->pluck('collections.id')->all())->toBe([$comics->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CollectionTypeUpdate,
    );
});

it('ignores collections that belong to another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $foreign = Collection::factory()->create();

    new SyncCollectionTypeCollections(user: $owner, collectionType: $type, collectionIds: [$foreign->id])->execute();

    expect($type->collections()->count())->toBe(0);
});

it('throws when a viewer tries to sync collections', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);

    $type = CollectionType::factory()->create(['account_id' => $account->id]);

    new SyncCollectionTypeCollections(user: $viewer, collectionType: $type, collectionIds: [])->execute();
});
