<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemViewEnum;
use App\Models\Collection;
use App\Models\CollectionView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CollectionView>
 */
class CollectionViewFactory extends Factory
{
    protected $model = CollectionView::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'collection_id' => Collection::factory(),
            'items_view' => ItemViewEnum::Grid->value,
        ];
    }
}
