<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SupportCategory;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => $this->faker->sentence(),
            'category' => $this->faker->randomElement(SupportCategory::cases()),
            'status' => SupportTicketStatus::Open,
        ];
    }

    /**
     * Indicate that the conversation is closed.
     */
    public function closed(SupportTicketCloser $closedBy = SupportTicketCloser::Team): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => SupportTicketStatus::Closed,
            'closed_by' => $closedBy,
            'closed_at' => now(),
        ]);
    }
}
