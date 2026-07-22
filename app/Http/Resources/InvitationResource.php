<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Invitation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Invitation
 */
class InvitationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'invitation',
            'id' => (string) $this->id,
            'attributes' => [
                'email' => $this->email,
                'role' => $this->role,
                'expires_at' => $this->expires_at?->timestamp,
                'accepted_at' => $this->accepted_at?->timestamp,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.account.invitations'),
            ],
        ];
    }
}
