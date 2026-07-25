<?php

declare(strict_types=1);
use App\Enums\SearchableEnum;
use App\Models\Catalog;
use Illuminate\Database\Eloquent\Relations\Relation;

it('uses the same aliases as the morph map', function () {
    foreach (SearchableEnum::cases() as $type) {
        expect(Relation::getMorphedModel($type->value))->toBe($type->modelClass());
    }
});

it('calls a catalog a collection', function () {
    expect(SearchableEnum::Catalog->value)->toBe('collection');
    expect(SearchableEnum::Catalog->modelClass())->toBe(Catalog::class);
    expect(SearchableEnum::Catalog->slug())->toBe('collections');
});

it('resolves a case from its url segment', function () {
    expect(SearchableEnum::fromSlug('items'))->toBe(SearchableEnum::Item);
    expect(SearchableEnum::fromSlug('spaceships'))->toBeNull();
});

it('gives every case a label, an icon and a badge', function () {
    foreach (SearchableEnum::cases() as $type) {
        expect($type->label())->not->toBeEmpty();
        expect($type->pluralLabel())->not->toBeEmpty();
        expect($type->icon())->not->toBeEmpty();
        expect($type->badgeClasses())->not->toBeEmpty();
    }
});

it('only holds back tags from a viewer', function () {
    $held = array_filter(SearchableEnum::cases(), fn (SearchableEnum $type): bool => $type->needsManagementAccess());

    expect(array_values($held))->toBe([SearchableEnum::Tag]);
});
