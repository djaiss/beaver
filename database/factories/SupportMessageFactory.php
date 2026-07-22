<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportMessage>
 */
class SupportMessageFactory extends Factory
{
    protected $model = SupportMessage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'support_ticket_id' => SupportTicket::factory(),
            'user_id' => User::factory(),
            'is_from_team' => false,
            'body' => $this->faker->paragraph(),
        ];
    }

    /**
     * Indicate that the message was written by the instance team.
     */
    public function fromTeam(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_from_team' => true,
        ]);
    }
}
