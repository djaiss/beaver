<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Copy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Copy
 */
class CopyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'copy',
            'id' => (string) $this->id,
            'attributes' => [
                'item_id' => (string) $this->item_id,
                'condition_id' => $this->condition_id !== null ? (string) $this->condition_id : null,
                'location_id' => $this->location_id !== null ? (string) $this->location_id : null,
                'acquired_at' => $this->acquired_at?->timestamp,
                'price_paid' => $this->price_paid,
                'estimated_value' => $this->estimated_value,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.items.copies.show', [$this->item_id, $this->id]),
            ],
        ];
    }
}
