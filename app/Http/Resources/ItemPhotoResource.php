<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ItemPhoto;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemPhoto
 */
class ItemPhotoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'item_photo',
            'id' => (string) $this->id,
            'attributes' => [
                'item_id' => (string) $this->item_id,
                'filename' => $this->filename,
                'mime_type' => $this->mime_type,
                'size' => $this->size,
                'is_main' => $this->is_main,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.items.photos.show', [$this->item_id, $this->id]),
            ],
        ];
    }
}
