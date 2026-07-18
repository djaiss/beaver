<?php

declare(strict_types=1);
use App\Actions\UpdateCollectionItemView;
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Models\Collection;
use App\Models\CollectionView;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('stores the chosen view for the user', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    $result = new UpdateCollectionItemView(
        user: $user,
        collection: $collection,
        view: ItemViewEnum::Table->value,
    )->execute();

    expect($result)->toBeInstanceOf(CollectionView::class);
    $this->assertDatabaseHas('collection_views', [
        'user_id' => $user->id,
        'collection_id' => $collection->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);
});

it('updates the existing preference without creating a duplicate', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    new UpdateCollectionItemView(user: $user, collection: $collection, view: ItemViewEnum::Table->value)->execute();
    new UpdateCollectionItemView(user: $user, collection: $collection, view: ItemViewEnum::List->value)->execute();

    expect(CollectionView::count())->toBe(1);
    expect($collection->viewForUser($user))->toBe(ItemViewEnum::List);
});

it('lets a viewer store their own view', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $collection = Collection::factory()->create(['account_id' => $account->id]);

    new UpdateCollectionItemView(user: $viewer, collection: $collection, view: ItemViewEnum::List->value)->execute();

    expect($collection->viewForUser($viewer))->toBe(ItemViewEnum::List);
});

it('does not touch the collection timestamp', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);
    $originalUpdatedAt = $collection->updated_at;

    $this->travel(1)->hour();
    new UpdateCollectionItemView(user: $user, collection: $collection, view: ItemViewEnum::Table->value)->execute();

    expect($collection->fresh()->updated_at->timestamp)->toBe($originalUpdatedAt->timestamp);
});

it('rejects a user who does not belong to the account', function () {
    $collection = Collection::factory()->create();
    $outsider = $this->createUser();

    expect(fn () => new UpdateCollectionItemView(
        user: $outsider,
        collection: $collection,
        view: ItemViewEnum::Grid->value,
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('rejects an invalid view', function () {
    $user = $this->createUser();
    $collection = Collection::factory()->create(['account_id' => $user->account_id]);

    expect(fn () => new UpdateCollectionItemView(
        user: $user,
        collection: $collection,
        view: 'carousel',
    )->execute())->toThrow(ValidationException::class);
});
