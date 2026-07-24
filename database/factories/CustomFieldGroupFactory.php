<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomFieldGroup>
 */
class CustomFieldGroupFactory extends Factory
{
    protected $model = CustomFieldGroup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_id' => CatalogType::factory(),
            'name' => fake()->randomElement(['Main', 'Details', 'Publishing info', 'Condition & grading', 'Origin']),
            'position' => 0,
        ];
    }
}
