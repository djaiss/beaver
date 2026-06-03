<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Member
 */
class MemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'member',
            'id' => (string) $this->id,
            'attributes' => [
                'user_id' => $this->user_id,
                'name' => $this->user?->getFullName(),
                'email' => $this->user?->email,
                'timezone' => $this->timezone,
                'joined_at' => $this->joined_at?->timestamp,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at->timestamp,
            ],
            'links' => [
                'self' => route('api.vault.adminland.member.show', [
                    'id' => $this->vault_id,
                    'memberId' => $this->id,
                ]),
            ],
        ];
    }
}
