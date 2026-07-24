<?php

declare(strict_types=1);
use App\Actions\UpdateCatalogItemView;
use App\Enums\ItemViewEnum;
use App\Enums\PermissionEnum;
use App\Models\Catalog;
use App\Models\CatalogView;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

it('stores the chosen view for the user', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    $result = new UpdateCatalogItemView(
        user: $user,
        catalog: $catalog,
        view: ItemViewEnum::Table->value,
    )->execute();

    expect($result)->toBeInstanceOf(CatalogView::class);
    $this->assertDatabaseHas('catalog_views', [
        'user_id' => $user->id,
        'catalog_id' => $catalog->id,
        'items_view' => ItemViewEnum::Table->value,
    ]);
});

it('updates the existing preference without creating a duplicate', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    new UpdateCatalogItemView(user: $user, catalog: $catalog, view: ItemViewEnum::Table->value)->execute();
    new UpdateCatalogItemView(user: $user, catalog: $catalog, view: ItemViewEnum::List->value)->execute();

    expect(CatalogView::count())->toBe(1);
    expect($catalog->viewForUser($user))->toBe(ItemViewEnum::List);
});

it('lets a viewer store their own view', function () {
    $account = $this->createAccount();
    $viewer = $this->createUser();
    $this->assignUserToAccount(user: $viewer, account: $account, role: PermissionEnum::Viewer->value);
    $catalog = Catalog::factory()->create(['account_id' => $account->id]);

    new UpdateCatalogItemView(user: $viewer, catalog: $catalog, view: ItemViewEnum::List->value)->execute();

    expect($catalog->viewForUser($viewer))->toBe(ItemViewEnum::List);
});

it('does not touch the collection timestamp', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);
    $originalUpdatedAt = $catalog->updated_at;

    $this->travel(1)->hour();
    new UpdateCatalogItemView(user: $user, catalog: $catalog, view: ItemViewEnum::Table->value)->execute();

    expect($catalog->fresh()->updated_at->timestamp)->toBe($originalUpdatedAt->timestamp);
});

it('rejects a user who does not belong to the account', function () {
    $catalog = Catalog::factory()->create();
    $outsider = $this->createUser();

    expect(fn () => new UpdateCatalogItemView(
        user: $outsider,
        catalog: $catalog,
        view: ItemViewEnum::Grid->value,
    )->execute())->toThrow(ModelNotFoundException::class);
});

it('rejects an invalid view', function () {
    $user = $this->createUser();
    $catalog = Catalog::factory()->create(['account_id' => $user->account_id]);

    expect(fn () => new UpdateCatalogItemView(
        user: $user,
        catalog: $catalog,
        view: 'carousel',
    )->execute())->toThrow(ValidationException::class);
});
