<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\EmailSent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin EmailSent
 */
class EmailSentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'email',
            'id' => (string) $this->id,
            'attributes' => [
                'email_type' => $this->email_type,
                'email_address' => $this->email_address,
                'subject' => $this->subject,
                'body' => $this->body,
                'sent_at' => $this->sent_at?->timestamp,
                'delivered_at' => $this->delivered_at?->timestamp,
                'bounced_at' => $this->bounced_at?->timestamp,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.administration.emails.show', $this->id),
            ],
        ];
    }
}
