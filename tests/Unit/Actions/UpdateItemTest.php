<?php

declare(strict_types=1);
use App\Actions\UpdateItem;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('updates an item and stamps the editor', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Chandler', 'last_name' => 'Bing']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'name' => 'Fantastic Four #1']);

    $updatedItem = new UpdateItem(
        user: $editor,
        item: $item,
        name: 'Amazing Spider-Man #1',
        description: 'The one Joey wants.',
    )->execute();

    expect($updatedItem)->toBeInstanceOf(Item::class);
    expect($updatedItem->name)->toBe('Amazing Spider-Man #1');
    expect($updatedItem->description)->toBe('The one Joey wants.');
    expect($updatedItem->updated_by_name)->toBe('Chandler Bing');

    $rawName = DB::table('items')->where('id', $item->id)->value('name');
    expect(decrypt($rawName, false))->toBe('Amazing Spider-Man #1');

    $this->assertDatabaseHas('items', [
        'id' => $item->id,
        'updated_by_id' => $editor->id,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::ItemUpdate,
    );
});

it('updates an item with a type linked to the collection', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($collectionType);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $updatedItem = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        collectionType: $collectionType,
    )->execute();

    expect($updatedItem->type_id)->toBe($collectionType->id);
});

it('throws when the type is not linked to the collection', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $unlinkedType = CollectionType::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        collectionType: $unlinkedType,
    )->execute();
});

it('clears the type when none is given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($collectionType);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $collectionType->id]);

    $updatedItem = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
    )->execute();

    expect($updatedItem->type_id)->toBeNull();
});

it('sanitizes the name and the description', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $updatedItem = new UpdateItem(
        user: $owner,
        item: $item,
        name: '<strong>Amazing Spider-Man #1</strong>',
        description: '<em>The one Joey wants.</em>',
    )->execute();

    expect($updatedItem->name)->toBe('Amazing Spider-Man #1');
    expect($updatedItem->description)->toBe('The one Joey wants.');
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new UpdateItem(
        user: $viewer,
        item: $item,
        name: 'Amazing Spider-Man #1',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $stranger = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    new UpdateItem(
        user: $stranger,
        item: $item,
        name: 'Amazing Spider-Man #1',
    )->execute();
});
