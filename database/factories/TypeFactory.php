<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Type;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Type>
 */
class TypeFactory extends Factory
{
    protected $model = Type::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['Comics', 'Vinyl', 'Wine', 'Stamps', 'Coins']),
            'color' => fake()->hexColor(),
        ];
    }
}
