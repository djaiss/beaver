<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Collection;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'type_id' => null,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
