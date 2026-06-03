<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Gender;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gender>
 */
class GenderFactory extends Factory
{
    protected $model = Gender::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'name' => $this->faker->randomElement(['Male', 'Female', 'Non-binary', 'Other', 'Prefer not to say']),
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
