<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Copy;
use App\Models\Location;
use App\Models\LocationHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LocationHistory>
 */
class LocationHistoryFactory extends Factory
{
    protected $model = LocationHistory::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'location_id' => Location::factory(),
            'moved_at' => fake()->date(),
            'moved_out_at' => null,
            'reason' => null,
            'note' => null,
        ];
    }

    /**
     * Indicate that the record is closed: the copy has since moved on.
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes): array => [
            'moved_out_at' => fake()->date(),
        ]);
    }
}
