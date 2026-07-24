<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\CatalogType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogType>
 */
class CatalogTypeFactory extends Factory
{
    protected $model = CatalogType::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['Comics', 'Vinyl', 'Wine', 'Stamps', 'Coins']),
            'color' => fake()->hexColor(),
        ];
    }
}
