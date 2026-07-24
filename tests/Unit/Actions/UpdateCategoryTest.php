<?php

declare(strict_types=1);
use App\Actions\UpdateCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('updates a category name and parent', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $parent = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Marvel']);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Old name']);

    $category = new UpdateCategory(
        user: $editor,
        category: $category,
        name: 'Spider-Man',
        parentId: $parent->id,
    )->execute();

    expect($category->name)->toBe('Spider-Man');
    expect($category->parent_id)->toBe($parent->id);
    expect($category->updated_by_id)->toBe($editor->id);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CategoryUpdate,
    );
});

it('updates the description, and clears it when none is given', function () {
    Queue::fake();

    $account = $this->createAccount();
    $editor = $this->createUser();
    $this->assignUserToAccount(user: $editor, account: $account, role: PermissionEnum::Editor->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id, 'description' => 'Old description']);

    $category = new UpdateCategory(
        user: $editor,
        category: $category,
        name: 'Spider-Man',
        description: '<strong>Key issues from the 1990s.</strong>',
    )->execute();

    expect($category->description)->toBe('Key issues from the 1990s.');

    $category = new UpdateCategory(
        user: $editor,
        category: $category,
        name: 'Spider-Man',
    )->execute();

    expect($category->description)->toBeNull();
});

it('throws when set as its own parent', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    new UpdateCategory(
        user: $owner,
        category: $category,
        name: 'Marvel',
        parentId: $category->id,
    )->execute();
});

it('throws when nested under one of its own descendants', function () {
    Queue::fake();
    $this->expectException(ValidationException::class);

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $parent = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Marvel']);
    $child = Category::factory()->create(['catalog_id' => $catalog->id, 'name' => 'Spider-Man', 'parent_id' => $parent->id]);

    new UpdateCategory(
        user: $owner,
        category: $parent,
        name: 'Marvel',
        parentId: $child->id,
    )->execute();
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['catalog_id' => $catalog->id]);

    new UpdateCategory(
        user: $viewer,
        category: $category,
        name: 'Marvel',
    )->execute();
});
