<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Condition>
 */
class ConditionFactory extends Factory
{
    protected $model = Condition::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['New', 'Like new', 'Used', 'Worn', 'Damaged']),
        ];
    }

    /**
     * Indicate that the condition is a system default, shared across all accounts.
     */
    public function systemDefault(): static
    {
        return $this->state(fn (array $attributes): array => [
            'account_id' => null,
        ]);
    }
}
