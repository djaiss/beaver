<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Person
 */
class PersonResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'person',
            'id' => (string) $this->id,
            'attributes' => [
                'gender_id' => $this->gender_id !== null ? (string) $this->gender_id : null,
                'kids_status' => $this->kids_status,
                'slug' => $this->slug,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'last_name' => $this->last_name,
                'nickname' => $this->nickname,
                'maiden_name' => $this->maiden_name,
                'suffix' => $this->suffix,
                'prefix' => $this->prefix,
                'can_be_deleted' => $this->can_be_deleted,
                'is_listed' => $this->is_listed,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.person.show', [
                    'id' => $this->vault_id,
                    'person' => $this->id,
                ]),
            ],
        ];
    }
}
