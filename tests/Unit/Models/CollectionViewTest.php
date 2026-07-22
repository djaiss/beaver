<?php

declare(strict_types=1);
use App\Enums\ItemViewEnum;
use App\Models\Collection;
use App\Models\CollectionView;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('casts the items view to the enum', function () {
    $view = CollectionView::factory()->create(['items_view' => ItemViewEnum::Table->value]);

    expect($view->items_view)->toBe(ItemViewEnum::Table);
});

it('belongs to a user', function () {
    $view = CollectionView::factory()->create();

    expect($view->user)->toBeInstanceOf(User::class);
    expect($view->user()->exists())->toBeTrue();
});

it('belongs to a collection', function () {
    $view = CollectionView::factory()->create();

    expect($view->collection)->toBeInstanceOf(Collection::class);
    expect($view->collection()->exists())->toBeTrue();
});
