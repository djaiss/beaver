<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Copy;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Copy>
 */
class CopyFactory extends Factory
{
    protected $model = Copy::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'condition_id' => null,
            'location_id' => null,
            'acquired_at' => fake()->date(),
            'price_paid' => fake()->numberBetween(100, 500000),
            'estimated_value' => fake()->numberBetween(100, 500000),
        ];
    }
}
