<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MaintenanceType;
use App\Models\Copy;
use App\Models\MaintenanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRecord>
 */
class MaintenanceRecordFactory extends Factory
{
    protected $model = MaintenanceRecord::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'provenance_event_id' => null,
            'type' => fake()->randomElement(MaintenanceType::cases()),
            'title' => 'Professional cleaning',
            'description' => null,
            'performed_by' => fake()->name(),
            'performed_at' => fake()->date(),
            'cost_amount' => fake()->numberBetween(0, 50000),
            'cost_currency_code' => 'USD',
            'condition_before_id' => null,
            'condition_after_id' => null,
            'next_due_at' => null,
            'include_in_provenance' => false,
        ];
    }
}
