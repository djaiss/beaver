<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\InsuranceStatus;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InsuranceRecord>
 */
class InsuranceRecordFactory extends Factory
{
    protected $model = InsuranceRecord::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'provider' => fake()->company(),
            'policy_number' => mb_strtoupper(fake()->bothify('??-#####')),
            'coverage_type' => 'Scheduled item',
            'insured_value' => fake()->numberBetween(1000, 5000000),
            'currency_code' => 'USD',
            'deductible_amount' => fake()->numberBetween(0, 50000),
            'deductible_currency_code' => 'USD',
            'starts_at' => fake()->date(),
            'ends_at' => null,
            'status' => InsuranceStatus::Active,
            'is_scheduled_item' => true,
            'contact_name' => null,
            'contact_email' => null,
            'contact_phone' => null,
            'note' => null,
        ];
    }
}
