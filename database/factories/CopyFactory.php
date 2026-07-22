<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CopyStatus;
use App\Models\Copy;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Copy>
 */
class CopyFactory extends Factory
{
    protected $model = Copy::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'identifier' => null,
            'item_condition_id' => null,
            'current_location_id' => null,
            'status' => CopyStatus::Owned,
            'quantity' => 1,
            'disposed_at' => null,
            'note' => null,
        ];
    }
}
