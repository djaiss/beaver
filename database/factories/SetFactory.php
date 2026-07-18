<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Set;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Set>
 */
class SetFactory extends Factory
{
    protected $model = Set::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
        ];
    }
}
