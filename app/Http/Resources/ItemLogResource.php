<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ItemLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemLog
 */
class ItemLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'item_log',
            'id' => (string) $this->id,
            'attributes' => [
                'user_name' => $this->getUserName(),
                'action' => $this->action,
                'parameters' => $this->parameters,
                'description' => $this->getTranslatedDescription(),
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.items.logs.show', [$this->item_id, $this->id]),
            ],
        ];
    }
}
