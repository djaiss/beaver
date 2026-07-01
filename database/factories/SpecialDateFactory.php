<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Person;
use App\Models\SpecialDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SpecialDate>
 */
class SpecialDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'person_id' => Person::factory(),
            'vault_id' => fn (array $attributes): int => Person::query()->findOrFail($attributes['person_id'])->vault_id,
            'should_be_reminded' => $this->faker->boolean(),
            'is_approximate' => $this->faker->boolean(),
            'year' => $this->faker->optional()->numberBetween(1900, 2100),
            'month' => $this->faker->numberBetween(1, 12),
            'day' => $this->faker->numberBetween(1, 28),
            'name' => $this->faker->randomElement(['Birthday', 'Anniversary', 'Memorial']),
        ];
    }
}
