<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CustomFieldGroup;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomFieldGroup
 */
class CustomFieldGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'custom_field_group',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.catalogTypes.customFieldGroups.show', [
                    'collectionType' => $this->type_id,
                    'group' => $this->id,
                ]),
            ],
        ];
    }
}
