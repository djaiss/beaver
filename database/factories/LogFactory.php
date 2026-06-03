<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Log;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Log>
 */
class LogFactory extends Factory
{
    protected $model = Log::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'vault_id' => Vault::factory(),
            'user_id' => User::factory(),
            'user_name' => fake()->name(),
            'action' => 'log.test.action',
            'parameters' => ['name' => fake()->word()],
        ];
    }
}
