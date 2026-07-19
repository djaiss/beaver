<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Collection;
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
            'collection_id' => Collection::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'target_count' => fake()->numberBetween(5, 50),
        ];
    }

    /**
     * Put the set in a collection of the given account, for the cases that only care which
     * account the set ends up under.
     */
    public function forAccount(Account|int $account): static
    {
        return $this->state(fn (): array => [
            'collection_id' => Collection::factory()->create([
                'account_id' => $account instanceof Account ? $account->id : $account,
            ]),
        ]);
    }
}
