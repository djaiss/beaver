<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Models\Copy;
use App\Models\Valuation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Valuation>
 */
class ValuationFactory extends Factory
{
    protected $model = Valuation::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'type' => ValuationType::UserEstimate,
            'amount' => fake()->numberBetween(100, 500000),
            'currency_code' => 'USD',
            'valued_at' => fake()->date(),
            'valuer' => null,
            'method' => null,
            'confidence' => ValuationConfidence::Unknown,
            'source_url' => null,
            'reference_number' => null,
            'note' => null,
        ];
    }
}
