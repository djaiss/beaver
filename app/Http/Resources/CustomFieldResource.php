<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CustomField;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomField
 */
class CustomFieldResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'custom_field',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'field_type' => $this->field_type->value,
                'options' => $this->options,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.collectionTypes.customFields.show', [
                    'collectionType' => $this->type_id,
                    'customField' => $this->id,
                ]),
            ],
        ];
    }
}
