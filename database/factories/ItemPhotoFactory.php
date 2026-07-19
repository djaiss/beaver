<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ItemPhoto>
 */
class ItemPhotoFactory extends Factory
{
    protected $model = ItemPhoto::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'path' => 'items/1/'.Str::uuid()->toString().'.jpg',
            'filename' => fake()->word().'.jpg',
            'mime_type' => 'image/jpeg',
            'size' => fake()->numberBetween(1000, 5000000),
            'width' => fake()->numberBetween(800, 4000),
            'height' => fake()->numberBetween(800, 4000),
            'is_main' => false,
            'position' => 1,
        ];
    }
}
