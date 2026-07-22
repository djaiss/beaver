<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TestimonialStatus;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    protected $model = Testimonial::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'link' => fake()->boolean() ? fake()->url() : null,
            'body' => fake()->paragraph(),
            'status' => TestimonialStatus::InReview,
            'submitted_at' => now(),
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => TestimonialStatus::Published,
            'published_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (): array => [
            'status' => TestimonialStatus::Rejected,
            'published_at' => null,
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => TestimonialStatus::Draft,
            'submitted_at' => null,
            'published_at' => null,
        ]);
    }
}
