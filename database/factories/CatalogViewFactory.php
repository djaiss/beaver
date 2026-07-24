<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemViewEnum;
use App\Models\Catalog;
use App\Models\CatalogView;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogView>
 */
class CatalogViewFactory extends Factory
{
    protected $model = CatalogView::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'catalog_id' => Catalog::factory(),
            'items_view' => ItemViewEnum::Grid->value,
        ];
    }
}
