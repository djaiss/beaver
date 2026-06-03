<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Vault>
 */
class VaultFactory extends Factory
{
    protected $model = Vault::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'slug' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Vault $vault): void {
            $vault->slug = $vault->id.'-'.Str::lower($vault->name);
            $vault->save();
        });
    }
}
