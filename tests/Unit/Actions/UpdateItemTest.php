<?php

declare(strict_types=1);
use App\Actions\UpdateItem;
use App\Enums\FieldTypeEnum;
use App\Enums\ItemActionEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Copy;
use App\Models\CustomField;
use App\Models\CustomFieldValue;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\Set;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

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

it('links the item to a category and a set, and clears them when none are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);
    $set = Set::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);

    $linked = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        category: $category,
        set: $set,
    )->execute();

    expect($linked->category_id)->toBe($category->id);
    expect($linked->set_id)->toBe($set->id);

    $cleared = new UpdateItem(
        user: $owner,
        item: $item->fresh(),
        name: 'Amazing Spider-Man #1',
    )->execute();

    expect($cleared->category_id)->toBeNull();
    expect($cleared->set_id)->toBeNull();
});

it('throws when the category belongs to another collection', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignCategory = Category::factory()->create();

    new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        category: $foreignCategory,
    )->execute();
});

it('syncs the tags and creates the new ones', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $keep = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Key issue']);
    $drop = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Duplicate']);
    $item->tags()->sync([$drop->id]);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        tagIds: [$keep->id],
        newTagNames: ['Silver age'],
    )->execute();

    $names = $updated->tags()->get()->map(fn (Tag $tag): string => $tag->name)->sort()->values()->all();

    expect($names)->toBe(['Key issue', 'Silver age']);
    expect($updated->tags()->whereKey($drop->id)->exists())->toBeFalse();
});

// The API only edits the catalog fields, so it passes no tags at all. That must
// leave the ones the item already carries alone rather than wiping them.
it('leaves the tags alone when none are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $tag = Tag::factory()->create(['account_id' => $account->id]);
    $item->tags()->sync([$tag->id]);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
    )->execute();

    expect($updated->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

it('throws when a tag belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignTag = Tag::factory()->create();

    new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        tagIds: [$foreignTag->id],
    )->execute();
});

it('creates, updates and clears custom field values', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $collectionType = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($collectionType);
    $issue = CustomField::factory()->create(['type_id' => $collectionType->id, 'field_type' => FieldTypeEnum::Text]);
    $notes = CustomField::factory()->create(['type_id' => $collectionType->id, 'field_type' => FieldTypeEnum::Text]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $collectionType->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $notes->id, 'value' => 'Gone soon']);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        collectionType: $collectionType,
        customFieldValues: [$issue->id => '1', $notes->id => ''],
    )->execute();

    $values = $updated->customFieldValues()->get()->keyBy('custom_field_id');

    expect($values->get($issue->id)->value)->toBe('1');
    expect($values->has($notes->id))->toBeFalse();

    // Saving the same field twice must edit the row rather than add a second one.
    new UpdateItem(
        user: $owner,
        item: $item->fresh(),
        name: 'Amazing Spider-Man #1',
        collectionType: $collectionType,
        customFieldValues: [$issue->id => '2'],
    )->execute();

    expect($item->customFieldValues()->where('custom_field_id', $issue->id)->count())->toBe(1);
    expect($item->customFieldValues()->where('custom_field_id', $issue->id)->first()->value)->toBe('2');
});

// A value whose field belongs to the old type can never be shown again, so
// changing the type drops it rather than leaving it orphaned.
it('drops the custom field values of the previous type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $comics = CollectionType::factory()->create(['account_id' => $account->id]);
    $vinyl = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach([$comics->id, $vinyl->id]);
    $issue = CustomField::factory()->create(['type_id' => $comics->id, 'field_type' => FieldTypeEnum::Text]);
    $item = Item::factory()->create(['collection_id' => $collection->id, 'type_id' => $comics->id]);
    CustomFieldValue::factory()->create(['item_id' => $item->id, 'custom_field_id' => $issue->id, 'value' => '1']);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        collectionType: $vinyl,
        customFieldValues: [],
    )->execute();

    expect($updated->customFieldValues()->count())->toBe(0);
});

it('adds, updates and deletes copies', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser(['first_name' => 'Monica', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $kept = Copy::factory()->create(['item_id' => $item->id, 'price_paid' => 100]);
    $removed = Copy::factory()->create(['item_id' => $item->id]);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        copies: [
            ['id' => $kept->id, 'price_paid' => 2500],
            ['estimated_value' => 9900],
        ],
    )->execute();

    expect($updated->copies()->count())->toBe(2);
    expect($kept->fresh()->price_paid)->toBe(2500);
    expect($kept->fresh()->updated_by_name)->toBe('Monica Geller');
    expect($updated->copies()->whereNull('id')->exists())->toBeFalse();
    expect($updated->copies()->where('estimated_value', 9900)->exists())->toBeTrue();

    $this->assertSoftDeleted('copies', ['id' => $removed->id]);
    expect($removed->fresh()->deleted_by_name)->toBe('Monica Geller');
});

it('throws when a copy belongs to another item', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $foreignCopy = Copy::factory()->create();

    new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        copies: [['id' => $foreignCopy->id]],
    )->execute();
});

it('leaves the copies alone when none are given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    Copy::factory()->create(['item_id' => $item->id]);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
    )->execute();

    expect($updated->copies()->count())->toBe(1);
});

// Only the very first photo of an item is promoted on its own, so replacing a
// cover has to hand the role over explicitly.
it('makes a new cover photo the main one', function () {
    Queue::fake();
    Storage::fake('local');

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create(['collection_id' => $collection->id]);
    $original = ItemPhoto::factory()->create(['item_id' => $item->id, 'is_main' => true]);

    $updated = new UpdateItem(
        user: $owner,
        item: $item,
        name: 'Amazing Spider-Man #1',
        coverPhoto: UploadedFile::fake()->image('cover.jpg'),
    )->execute();

    expect($updated->photos()->count())->toBe(2);
    expect($original->fresh()->is_main)->toBeFalse();
    expect($updated->mainPhoto()->first()->id)->not->toBe($original->id);
});

it('records the values that moved on the activity of the item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man']);
    $item = Item::factory()->create([
        'collection_id' => $collection->id,
        'name' => 'Amazing Spider-Man #1',
        'description' => 'The one with the duck.',
        'category_id' => null,
    ]);

    new UpdateItem(
        user: $editor,
        item: $item,
        name: 'Amazing Spider-Man #2',
        description: 'The one with the chick.',
        category: $category,
    )->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogItemAction::class,
        callback: fn (LogItemAction $job): bool => $job->action === ItemActionEnum::ItemUpdate
            && $job->parameters === ['changes' => [
                ['label' => 'Name', 'from' => 'Amazing Spider-Man #1', 'to' => 'Amazing Spider-Man #2'],
                // A description is too long for a chip, so only the fact it moved is kept.
                ['label' => 'Description'],
                ['label' => 'Category', 'from' => null, 'to' => 'Spider-Man'],
            ]],
    );
});

it('records no chips when nothing on the item moved', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $item = Item::factory()->create([
        'collection_id' => $collection->id,
        'name' => 'Amazing Spider-Man #1',
        'description' => null,
        'category_id' => null,
        'type_id' => null,
        'set_id' => null,
    ]);

    new UpdateItem(user: $editor, item: $item, name: 'Amazing Spider-Man #1')->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogItemAction::class,
        callback: fn (LogItemAction $job): bool => $job->parameters === null,
    );
});
