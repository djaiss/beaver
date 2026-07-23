<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\ItemCondition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemCondition>
 */
class ItemConditionFactory extends Factory
{
    protected $model = ItemCondition::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['New', 'Like new', 'Used', 'Worn', 'Damaged']),
            'position' => fake()->numberBetween(1, 5),
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
