<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Item
 */
class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'item',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'description' => $this->description,
                'collection_id' => (string) $this->collection_id,
                'type_id' => $this->type_id !== null ? (string) $this->type_id : null,
                'category_id' => $this->category_id !== null ? (string) $this->category_id : null,
                'set_id' => $this->set_id !== null ? (string) $this->set_id : null,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.collections.items.show', [$this->collection_id, $this->id]),
            ],
        ];
    }
}
