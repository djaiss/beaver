<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Location>
 */
class LocationFactory extends Factory
{
    protected $model = Location::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => Account::factory(),
            'parent_id' => null,
            'name' => fake()->randomElement(['Shelf A', 'Shelf B', 'Box 1', 'Display case', 'Attic', 'Garage']),
        ];
    }
}
