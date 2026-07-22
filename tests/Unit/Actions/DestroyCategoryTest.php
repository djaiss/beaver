<?php

declare(strict_types=1);
use App\Actions\DestroyCategory;
use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a category', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);

    new DestroyCategory(
        user: $owner,
        category: $category,
    )->execute();

    $this->assertSoftDeleted('categories', ['id' => $category->id]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::CategoryDeletion,
    );
});

it('cascades the delete to child categories', function () {
    Queue::fake();

    $account = $this->createAccount();
    $owner = $this->createUser();
    $this->assignUserToAccount(user: $owner, account: $account, role: PermissionEnum::Owner->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $parent = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Marvel']);
    $child = Category::factory()->create(['collection_id' => $collection->id, 'name' => 'Spider-Man', 'parent_id' => $parent->id]);

    new DestroyCategory(
        user: $owner,
        category: $parent,
    )->execute();

    $this->assertSoftDeleted('categories', ['id' => $child->id]);
});

it('throws when the user is only a viewer', function () {
    Queue::fake();
    $this->expectException(ModelNotFoundException::class);

    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);
    $category = Category::factory()->create(['collection_id' => $collection->id]);

    new DestroyCategory(
        user: $viewer,
        category: $category,
    )->execute();
});
