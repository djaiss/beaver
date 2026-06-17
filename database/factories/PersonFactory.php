<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Gender;
use App\Models\Person;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    protected $model = Person::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'gender_id' => Gender::factory(),
            'kids_status' => $this->faker->randomElement(['has_kids', 'no_kids', 'unknown']),
            'slug' => $this->faker->unique()->slug(),
            'first_name' => $this->faker->firstName(),
            'middle_name' => $this->faker->optional()->firstName(),
            'last_name' => $this->faker->lastName(),
            'nickname' => $this->faker->optional()->firstName(),
            'maiden_name' => $this->faker->optional()->lastName(),
            'suffix' => $this->faker->optional()->word(),
            'prefix' => $this->faker->optional()->title(),
            'can_be_deleted' => true,
            'is_listed' => true,
        ];
    }
}
