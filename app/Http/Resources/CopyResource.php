<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Copy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The estimated value is not a column on the copy any more, it is read from the
 * latest valuation. It stays in the payload because it is still what the copy is
 * currently worth, but it is read only: writing it records a valuation.
 *
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
                'identifier' => $this->identifier,
                'condition_id' => $this->condition_id !== null ? (string) $this->condition_id : null,
                'current_location_id' => $this->current_location_id !== null ? (string) $this->current_location_id : null,
                'status' => $this->status->value,
                'quantity' => $this->quantity,
                'disposed_at' => $this->disposed_at?->timestamp,
                'note' => $this->note,
                'estimated_value' => $this->estimatedValue(),
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.items.copies.show', [$this->item_id, $this->id]),
            ],
        ];
    }
}
