<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\RelationshipType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin RelationshipType
 */
class RelationshipTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'relationship_type',
            'id' => (string) $this->id,
            'attributes' => [
                'relationship_type_category_id' => (string) $this->relationship_type_category_id,
                'key' => $this->key,
                'name' => $this->name,
                'forward_name' => $this->forward_name,
                'reverse_name' => $this->reverse_name,
                'is_directed' => $this->is_directed,
                'can_be_deleted' => $this->can_be_deleted,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.relationship_type.show', [
                    'id' => $this->vault_id,
                    'relationshipTypeCategory' => $this->relationship_type_category_id,
                    'relationshipType' => $this->id,
                ]),
            ],
        ];
    }
}
