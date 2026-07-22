<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Category;
use App\Models\Collection;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Set;

/**
 * The kinds of object the trash screen can list and restore. Every case maps to
 * a model that soft deletes.
 */
enum TrashableEnum: string
{
    case Collection = 'collection';
    case Item = 'item';
    case Copy = 'copy';
    case Category = 'category';
    case Set = 'set';

    /**
     * @return class-string<Category|Collection|Copy|Item|Set>
     */
    public function modelClass(): string
    {
        return match ($this) {
            self::Collection => Collection::class,
            self::Item => Item::class,
            self::Copy => Copy::class,
            self::Category => Category::class,
            self::Set => Set::class,
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::Collection => __('Collection'),
            self::Item => __('Item'),
            self::Copy => __('Copy'),
            self::Category => __('Category'),
            self::Set => __('Set'),
        };
    }

    public function pluralLabel(): string
    {
        return match ($this) {
            self::Collection => __('Collections'),
            self::Item => __('Items'),
            self::Copy => __('Copies'),
            self::Category => __('Categories'),
            self::Set => __('Sets'),
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Collection => 'folder',
            self::Item => 'package',
            self::Copy => 'copy',
            self::Category => 'layout-grid',
            self::Set => 'boxes',
        };
    }

    /**
     * The Tailwind classes colouring the type badge, matching the design.
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::Collection => 'bg-badge-violet/15 text-badge-violet',
            self::Item => 'bg-badge-emerald/15 text-badge-emerald',
            self::Copy => 'bg-badge-orange/15 text-badge-orange',
            self::Category => 'bg-brand/15 text-brand',
            self::Set => 'bg-badge-pink/15 text-badge-pink',
        };
    }
}
