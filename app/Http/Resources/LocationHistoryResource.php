<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\LocationHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin LocationHistory
 */
class LocationHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'location_history',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'location_id' => $this->location_id !== null ? (string) $this->location_id : null,
                'moved_at' => $this->moved_at->timestamp,
                'moved_out_at' => $this->moved_out_at?->timestamp,
                'reason' => $this->reason,
                'note' => $this->note,
                'is_open' => $this->isOpen(),
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.locationHistory.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
