<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Vault;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Vault
 */
class VaultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'vault',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'avatar' => $this->getAvatar(),
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.show', $this->id),
            ],
        ];
    }
}
