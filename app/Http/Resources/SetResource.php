<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Set;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Set
 */
class SetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'set',
            'id' => (string) $this->id,
            'attributes' => [
                'collection_id' => $this->collection_id,
                'name' => $this->name,
                'description' => $this->description,
                'target_count' => $this->target_count,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.sets.show', $this->id),
            ],
        ];
    }
}
