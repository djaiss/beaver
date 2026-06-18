<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Gender;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Gender
 */
class GenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'gender',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'position' => $this->position,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.gender.show', [
                    'id' => $this->vault_id,
                    'gender' => $this->id,
                ]),
            ],
        ];
    }
}
