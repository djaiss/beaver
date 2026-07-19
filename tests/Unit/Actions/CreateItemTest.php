<?php

declare(strict_types=1);
use App\Actions\CreateItem;
use App\Enums\CopyStatus;
use App\Enums\FieldTypeEnum;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\Collection;
use App\Models\CollectionType;
use App\Models\Condition;
use App\Models\CustomField;
use App\Models\Item;
use App\Models\Location;
use App\Models\Series;
use App\Models\Set;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

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

it('creates the copies along with the item', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);
    $condition = Condition::factory()->create(['account_id' => $account->id]);
    $location = Location::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        copies: [
            [
                'identifier' => 'CGC 1234567',
                'condition_id' => $condition->id,
                'current_location_id' => $location->id,
                'status' => CopyStatus::Loaned,
                'quantity' => 2,
                'disposed_at' => null,
                'note' => 'Lent to Joey.',
                'estimated_value' => 9900,
            ],
            [],
        ],
    )->execute();

    expect($item->copies)->toHaveCount(2);

    $first = $item->copies->first();

    expect($first->condition_id)->toBe($condition->id);
    expect($first->current_location_id)->toBe($location->id);
    expect($first->identifier)->toBe('CGC 1234567');
    expect($first->status)->toBe(CopyStatus::Loaned);
    expect($first->quantity)->toBe(2);
    expect($first->note)->toBe('Lent to Joey.');
    expect($first->created_by_id)->toBe($owner->id);

    // A copy row that says nothing falls back to one owned copy.
    expect($item->copies->last()->status)->toBe(CopyStatus::Owned);
    expect($item->copies->last()->quantity)->toBe(1);
});

// The estimated value is no longer a column, so a figure given with a copy row
// has to open that copy's valuation history instead.
it('records the estimated value of a copy as a valuation', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id, 'currency' => 'USD']);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        copies: [['estimated_value' => 42000]],
    )->execute();

    $copy = $item->copies->first();

    expect($copy->estimatedValue())->toBe(42000);
    expect($copy->valuations()->count())->toBe(1);

    $valuation = $copy->valuations()->first();

    expect($valuation->type)->toBe(ValuationType::UserEstimate);
    expect($valuation->currency_code)->toBe('USD');
    expect($valuation->confidence)->toBe(ValuationConfidence::Unknown);
    expect($valuation->valued_at->toDateString())->toBe(now()->toDateString());
    expect($valuation->created_by_id)->toBe($owner->id);
});

it('leaves a copy without any valuation when no estimated value is given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        copies: [['condition_id' => null]],
    )->execute();

    expect($item->copies->first()->valuations()->count())->toBe(0);
    expect($item->copies->first()->estimatedValue())->toBeNull();
});

it('throws when a copy condition belongs to another account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $foreignCondition = Condition::factory()->create();

    new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        copies: [['condition_id' => $foreignCondition->id]],
    )->execute();

    expect(Item::query()->count())->toBe(0);
});

it('attaches existing tags and creates new ones', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $signed = Tag::factory()->create(['account_id' => $account->id, 'name' => 'Signed']);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        tagIds: [$signed->id],
        newTagNames: ['First Issue'],
    )->execute();

    expect($item->tags)->toHaveCount(2);
    expect($item->tags->pluck('name'))->toContain('Signed', 'First Issue');
    expect($account->tags()->count())->toBe(2);
});

it('records custom field values for the selected type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($type);
    $issue = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Number]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        collectionType: $type,
        customFieldValues: [$issue->id => '300'],
    )->execute();

    expect($item->customFieldValues)->toHaveCount(1);
    expect($item->customFieldValues->first()->value)->toBe('300');
    expect($item->customFieldValues->first()->custom_field_id)->toBe($issue->id);
});

it('records a rating custom field value', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($type);
    $rating = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Rating]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'The One With The Embryos',
        collectionType: $type,
        customFieldValues: [$rating->id => '4'],
    )->execute();

    expect($item->customFieldValues)->toHaveCount(1);
    expect($item->customFieldValues->first()->value)->toBe('4');
});

it('drops a rating that falls outside the one to five scale', function (string $value) {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($type);
    $rating = CustomField::factory()->create(['type_id' => $type->id, 'field_type' => FieldTypeEnum::Rating]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'The One With The Embryos',
        collectionType: $type,
        customFieldValues: [$rating->id => $value],
    )->execute();

    expect($item->customFieldValues)->toHaveCount(0);
})->with(['0', '6', '-1', '3.5', 'five']);

it('ignores custom field values that do not belong to the type', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $type = CollectionType::factory()->create(['account_id' => $account->id]);
    $collection->collectionTypes()->attach($type);
    $foreignField = CustomField::factory()->create();

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        collectionType: $type,
        customFieldValues: [$foreignField->id => '300'],
    )->execute();

    expect($item->customFieldValues)->toHaveCount(0);
});

it('links the item to a category and a set', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);
    $set = Set::factory()->create(['collection_id' => $collection->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        category: $category,
        set: $set,
    )->execute();

    expect($item->category_id)->toBe($category->id);
    expect($item->set_id)->toBe($set->id);
});

it('throws when the category belongs to another collection', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $foreignCategory = Category::factory()->create();

    new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        category: $foreignCategory,
    )->execute();
});

it('adds several photos to a new item, the first becoming the cover', function () {
    Queue::fake();
    Storage::fake('local');

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
        photos: [
            UploadedFile::fake()->image('front.jpg'),
            UploadedFile::fake()->image('back.jpg'),
        ],
    )->execute();

    expect($item->photos()->count())->toBe(2);
    expect($item->mainPhoto()->first()->filename)->toBe('front.jpg');
    // The order the photos were given is the order the item screen shows them.
    expect($item->photos()->pluck('filename')->all())->toBe(['front.jpg', 'back.jpg']);
});

it('creates an item without any photo', function () {
    Queue::fake();
    Storage::fake('local');

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Amazing Spider-Man #1',
    )->execute();

    expect($item->photos()->count())->toBe(0);
    expect($item->mainPhoto()->first())->toBeNull();
});

it('links the item to a series from another collection of the account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    // The series was born in the books collection but the item lands in the films one.
    // A series is account-wide, so that is allowed where a set would not be.
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $series = Series::factory()->create(['account_id' => $account->id]);

    $item = new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'The Empire Strikes Back (4K)',
        series: $series,
    )->execute();

    expect($item->series_id)->toBe($series->id);
});

it('refuses a series from another account', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);

    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $series = Series::factory()->create();

    new CreateItem(
        user: $owner,
        collection: $collection,
        name: 'Millennium Falcon 75192',
        series: $series,
    )->execute();
})->throws(ModelNotFoundException::class);
