<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FieldTypeEnum;
use App\Models\CollectionType;
use App\Models\CustomField;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CustomField>
 */
class CustomFieldFactory extends Factory
{
    protected $model = CustomField::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type_id' => CollectionType::factory(),
            'name' => fake()->randomElement(['Issue #', 'Vintage', 'Grade', 'Year', 'Publisher']),
            'field_type' => FieldTypeEnum::Text->value,
            'options' => null,
            'position' => 0,
        ];
    }
}
