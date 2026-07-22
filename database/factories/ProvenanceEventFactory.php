<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DatePrecision;
use App\Enums\ProvenanceEventType;
use App\Models\Copy;
use App\Models\ProvenanceEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProvenanceEvent>
 */
class ProvenanceEventFactory extends Factory
{
    protected $model = ProvenanceEvent::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'copy_id' => Copy::factory(),
            'transaction_id' => null,
            'type' => ProvenanceEventType::Acquisition,
            'title' => fake()->sentence(4),
            'description' => null,
            'occurred_at' => fake()->date(),
            'occurred_at_precision' => DatePrecision::Exact,
            'location' => null,
            'from_party' => null,
            'to_party' => null,
            'reference_number' => null,
            'source_url' => null,
            'is_verified' => false,
            'verification_note' => null,
        ];
    }
}
