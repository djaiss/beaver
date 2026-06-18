<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RelationshipTypeCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RelationshipTypeCategory
 */
class RelationshipTypeCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'relationship_type_category',
            'id' => (string) $this->id,
            'attributes' => [
                'key' => $this->key,
                'name' => $this->name,
                'position' => $this->position,
                'can_be_deleted' => $this->can_be_deleted,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.relationship_type_category.show', [
                    'id' => $this->vault_id,
                    'relationshipTypeCategory' => $this->id,
                ]),
            ],
        ];
    }
}
