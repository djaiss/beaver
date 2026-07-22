<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'collection_id' => Collection::factory(),
            'parent_id' => null,
            'name' => fake()->randomElement(['Spider-Man', 'X-Men', 'Avengers', 'Fantastic Four']),
            'description' => fake()->sentence(),
        ];
    }
}
