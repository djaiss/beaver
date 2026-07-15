<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Condition
 */
class ConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'condition',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.conditions.show', $this->id),
            ],
        ];
    }
}
