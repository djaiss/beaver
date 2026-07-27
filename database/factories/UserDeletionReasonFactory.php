<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\UserDeletionReason;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDeletionReason>
 */
class UserDeletionReasonFactory extends Factory
{
    protected $model = UserDeletionReason::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reason' => $this->faker->sentence(),
        ];
    }
}
