<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'location',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'emoji' => $this->emoji,
                'parent_id' => $this->parent_id !== null ? (string) $this->parent_id : null,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.locations.show', $this->id),
            ],
        ];
    }
}
