<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'name' => fake()->randomElement(['Signed', 'First Issue', 'Limited Edition', 'Rare', 'Sealed']),
        ];
    }
}
