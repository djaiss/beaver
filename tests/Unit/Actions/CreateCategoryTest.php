<?php

declare(strict_types=1);
use App\Actions\CreateCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('creates a category and stamps the author', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser(['first_name' => 'Ross', 'last_name' => 'Geller']);
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $category = new CreateCategory(
        user: $editor,
        collection: $collection,
        name: 'Marvel',
    )->execute();

    expect($category)->toBeInstanceOf(Category::class);
    expect($category->name)->toBe('Marvel');
    expect($category->collection_id)->toBe($collection->id);
    expect($category->parent_id)->toBeNull();

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'collection_id' => $collection->id,
        'created_by_id' => $editor->id,
        'updated_by_id' => $editor->id,
    ]);
    expect($category->created_by_name)->toBe('Ross Geller');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CategoryCreation,
    );
});

it('creates a nested category', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $parent = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Marvel']);

    $child = new CreateCategory(
        user: $owner,
        collection: $collection,
        name: 'Spider-Man',
        parentId: $parent->id,
    )->execute();

    expect($child->parent_id)->toBe($parent->id);
});

it('sanitizes the name', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    $category = new CreateCategory(
        user: $owner,
        collection: $collection,
        name: '<strong>Marvel</strong>',
    )->execute();

    expect($category->name)->toBe('Marvel');
});

it('throws when the parent belongs to another collection', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $foreignCategory = Category::factory()->create();

    new CreateCategory(
        user: $owner,
        collection: $collection,
        name: 'Spider-Man',
        parentId: $foreignCategory->id,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new CreateCategory(
        user: $viewer,
        collection: $collection,
        name: 'Marvel',
    )->execute();
});

it('throws when the user does not belong to the account', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $collection = Collection::factory()->create();
    $stranger = $this->createUser();

    new CreateCategory(
        user: $stranger,
        collection: $collection,
        name: 'Marvel',
    )->execute();
});
