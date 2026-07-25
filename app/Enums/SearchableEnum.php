<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use App\Models\ItemPhoto;
use App\Models\Loan;
use App\Models\Location;
use App\Models\Series;
use App\Models\Set;
use App\Models\Tag;

/**
 * The kinds of record account wide search indexes and groups its results by.
 *
 * The value of each case is the polymorphic alias written in
 * search_tokens.searchable_type, pinned in AppServiceProvider. The cases are
 * declared in the order the groups appear on the search screen, so a result
 * about an item is read before one about the tag that led to it.
 */
enum SearchableEnum: string
{
    case Item = 'item';
    case Catalog = 'collection';
    case Copy = 'copy';
    case Photo = 'photo';
    case Loan = 'loan';
    case Location = 'location';
    case Set = 'set';
    case Series = 'series';
    case Category = 'category';
    case Tag = 'tag';
    case Document = 'document';

    /**
     * @return class-string<Catalog|Category|Copy|Document|Item|ItemPhoto|Loan|Location|Series|Set|Tag>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Item => Item::class,
            self::Catalog => Catalog::class,
            self::Copy => Copy::class,
            self::Photo => ItemPhoto::class,
            self::Loan => Loan::class,
            self::Location => Location::class,
            self::Set => Set::class,
            self::Series => Series::class,
            self::Category => Category::class,
            self::Tag => Tag::class,
            self::Document => Document::class,
        };
    }

    /**
     * The heading of the group of results.
     */
    public function pluralLabel(): string
    {
        return match ($this) {
            self::Item => __('Items'),
            self::Catalog => __('Collections'),
            self::Copy => __('Copies'),
            self::Photo => __('Photos'),
            self::Loan => __('Loans'),
            self::Location => __('Locations'),
            self::Set => __('Sets'),
            self::Series => __('Series'),
            self::Category => __('Categories'),
            self::Tag => __('Tags'),
            self::Document => __('Documents'),
        };
    }

    /**
     * The badge on a single result row.
     */
    public function label(): string
    {
        return match ($this) {
            self::Item => __('Item'),
            self::Catalog => __('Collection'),
            self::Copy => __('Copy'),
            self::Photo => __('Photo'),
            self::Loan => __('Loan'),
            self::Location => __('Location'),
            self::Set => __('Set'),
            self::Series => __('Series'),
            self::Category => __('Category'),
            self::Tag => __('Tag'),
            self::Document => __('Document'),
        };
    }

    /**
     * The segment of the URL that narrows the results to this kind of record.
     */
    public function slug(): string
    {
        return match ($this) {
            self::Item => 'items',
            self::Catalog => 'collections',
            self::Copy => 'copies',
            self::Photo => 'photos',
            self::Loan => 'loans',
            self::Location => 'locations',
            self::Set => 'sets',
            self::Series => 'series',
            self::Category => 'categories',
            self::Tag => 'tags',
            self::Document => 'documents',
        };
    }

    public static function fromSlug(string $slug): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->slug() === $slug) {
                return $case;
            }
        }

        return null;
    }

    public function icon(): string
    {
        return match ($this) {
            self::Item => 'package',
            self::Catalog => 'layers',
            self::Copy => 'copy',
            self::Photo => 'image',
            self::Loan => 'arrow-left-right',
            self::Location => 'map-pin',
            self::Set => 'boxes',
            self::Series => 'library',
            self::Category => 'folder-tree',
            self::Tag => 'tag',
            self::Document => 'file-text',
        };
    }

    /**
     * The Tailwind classes colouring the type badge, matching the design.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Item => 'bg-badge-emerald/15 text-badge-emerald',
            self::Catalog => 'bg-badge-violet/15 text-badge-violet',
            self::Copy => 'bg-badge-orange/15 text-badge-orange',
            self::Photo => 'bg-badge-pink/15 text-badge-pink',
            self::Loan => 'bg-brand/15 text-brand',
            self::Location => 'bg-badge-emerald/15 text-badge-emerald',
            self::Set => 'bg-badge-orange/15 text-badge-orange',
            self::Series => 'bg-badge-violet/15 text-badge-violet',
            self::Category => 'bg-brand/15 text-brand',
            self::Tag => 'bg-badge-pink/15 text-badge-pink',
            self::Document => 'bg-badge-orange/15 text-badge-orange',
        };
    }

    /**
     * What a record of this kind has to have loaded before it can describe
     * itself: the relations its indexed text, its context line and its link
     * read. Everything here is loaded in one query per kind rather than one per
     * result.
     *
     * @return list<string>
     */
    public function relations(): array
    {
        return match ($this) {
            self::Item => ['catalog', 'category', 'set', 'series', 'catalogType', 'tags', 'customFieldValues', 'mainPhoto'],
            self::Catalog => [],
            self::Copy => ['item.catalog', 'item.mainPhoto', 'currentLocation', 'itemCondition'],
            self::Photo => ['item.catalog'],
            self::Loan => ['copy.item.catalog', 'copy.item.mainPhoto'],
            self::Location => ['parent'],
            self::Set => ['catalog'],
            self::Series => [],
            self::Category => ['catalog', 'parent'],
            self::Tag => [],
            self::Document => ['documentable'],
        };
    }

    /**
     * The relations a context line only needs the size of.
     *
     * @return list<string>
     */
    public function relationCounts(): array
    {
        return match ($this) {
            self::Catalog, self::Set, self::Series, self::Category, self::Tag => ['items'],
            self::Item => ['copies'],
            self::Location => ['copies'],
            default => [],
        };
    }

    /**
     * Only owners and editors have a screen for a tag, so a viewer is never
     * offered one as a result.
     */
    public function needsManagementAccess(): bool
    {
        return $this === self::Tag;
    }
}
