<?php

declare(strict_types=1);
use App\Actions\CreateItem;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates an item and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $editor,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        description: 'The one Joey wants.',
    )->execute();

    expect($item)->toBeInstanceOf(Item::class);
    expect($item->name)->toBe('Amazing Spider-Man #1');
    expect($item->description)->toBe('The one Joey wants.');
    expect($item->collection_id)->toBe($collection->id);
    expect($item->type_id)->toBeNull();

    $this->assertDatabaseHas('items', [
        'id' => $item->id,
        'collection_id' => $collection->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($item->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemCreation,
    );
});

it('creates an item with a type linked to the collection', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($collectionType);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        collectionType: $collectionType,
    )->execute();

    expect($item->type_id)->toBe($collectionType->id);
});

it('throws when the type is not linked to the collection', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $unlinkedType = CollectionType::factory()->create(['account_id' => $account->id]);

    new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        collectionType: $unlinkedType,
    )->execute();
});

it('sanitizes the name and the description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: '<strong>Amazing Spider-Man #1</strong>',
        description: '<em>The one Joey wants.</em>',
    )->execute();

    expect($item->name)->toBe('Amazing Spider-Man #1');
    expect($item->description)->toBe('The one Joey wants.');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new CreateItem(
        user: $viewer,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new CreateItem(
        user: $stranger,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
    )->execute();
});
