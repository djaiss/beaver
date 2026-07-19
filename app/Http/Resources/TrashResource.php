<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Wraps one entry of the Trash service, which hands out plain arrays rather
 * than models because it spans the five tables that soft delete.
 */
class TrashResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'trashed_object',
            'id' => (string) $this->resource['id'],
            'attributes' => [
                'object_type' => $this->resource['type']->value,
                'name' => $this->resource['name'],
                'subtitle' => $this->resource['subtitle'],
                'deleted_at' => $this->resource['deleted_at']?->timestamp,
                'deleted_by_name' => $this->resource['deleted_by_name'],
                'days_left' => $this->resource['days_left'],
            ],
        ];
    }
}
