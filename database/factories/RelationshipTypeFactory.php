<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RelationshipType>
 */
class RelationshipTypeFactory extends Factory
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
            'relationship_type_category_id' => RelationshipTypeCategory::factory(),
            'key' => $this->faker->unique()->slug(2),
            'name' => $this->faker->randomElement(['Parent', 'Sibling', 'Friend', 'Colleague']),
            'forward_name' => $this->faker->randomElement(['Parent of', 'Sibling of', 'Friend of', 'Colleague of']),
            'reverse_name' => $this->faker->randomElement(['Child of', 'Sibling of', 'Friend of', 'Colleague of']),
            'is_directed' => $this->faker->boolean(),
            'can_be_deleted' => true,
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
