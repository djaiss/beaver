<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Log
 */
class LogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'log',
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
                'self' => route('api.administration.logs.show', $this->id),
            ],
        ];
    }
}
