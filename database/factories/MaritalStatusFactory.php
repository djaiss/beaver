<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MaritalStatus;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaritalStatus>
 */
class MaritalStatusFactory extends Factory
{
    protected $model = MaritalStatus::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'name' => $this->faker->randomElement(['Single', 'Married', 'In a relationship', 'Divorced', 'Widowed']),
            'position' => $this->faker->numberBetween(1, 100),
        ];
    }
}
