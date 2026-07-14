<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VisibilityEnum;
use App\Models\Account;
use App\Models\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Collection>
 */
class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid' => (string) Str::uuid(),
            'account_id' => Account::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'emoji' => fake()->randomElement(['📚', '💿', '🍷', '🎨', '🪙', '📦']),
            'visibility' => VisibilityEnum::Private->value,
            'currency' => 'USD',
            'settings' => null,
        ];
    }
}
