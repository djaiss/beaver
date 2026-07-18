<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ItemActionEnum;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemLog>
 */
class ItemLogFactory extends Factory
{
    protected $model = ItemLog::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'user_id' => User::factory(),
            'user_name' => fake()->name(),
            'action' => ItemActionEnum::ItemCreation->value,
            'parameters' => null,
        ];
    }
}
