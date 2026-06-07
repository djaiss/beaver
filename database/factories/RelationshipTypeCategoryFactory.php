<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RelationshipTypeCategory;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RelationshipTypeCategory>
 */
class RelationshipTypeCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'key' => $this->faker->unique()->slug(2),
            'name' => $this->faker->randomElement(['Family', 'Friends', 'Work']),
            'position' => $this->faker->numberBetween(1, 100),
            'can_be_deleted' => true,
        ];
    }
}
