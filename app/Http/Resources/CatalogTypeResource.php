<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CatalogType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CatalogType
 */
class CatalogTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'collection_type',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'color' => $this->color,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.catalogTypes.show', $this->id),
            ],
        ];
    }
}
