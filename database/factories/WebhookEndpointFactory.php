<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use App\Models\WebhookEndpoint;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<WebhookEndpoint>
 */
class WebhookEndpointFactory extends Factory
{
    protected $model = WebhookEndpoint::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'label' => $this->faker->words(2, true),
            'url' => $this->faker->url(),
            'secret' => Str::random(64),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the endpoint is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_active' => false,
        ]);
    }
}
