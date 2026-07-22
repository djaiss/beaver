<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ProvenanceEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * No amounts live here. Financial data belongs to the transaction, so an event
 * that came from an exchange carries its id rather than restating what was paid.
 *
 * The date is paired with how much of it is known, and with the rendering of it
 * at that precision: a raw timestamp alone would read as a full day even when
 * only the year is actually known.
 *
 * @mixin ProvenanceEvent
 */
class ProvenanceEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'provenance_event',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'transaction_id' => $this->transaction_id === null ? null : (string) $this->transaction_id,
                'type' => $this->type->value,
                'title' => $this->title,
                'description' => $this->description,
                'occurred_at' => $this->occurred_at?->timestamp,
                'occurred_at_precision' => $this->occurred_at_precision->value,
                'formatted_date' => $this->formattedDate(),
                'location' => $this->location,
                'from_party' => $this->from_party,
                'to_party' => $this->to_party,
                'reference_number' => $this->reference_number,
                'source_url' => $this->source_url,
                'is_verified' => $this->is_verified,
                'verification_note' => $this->verification_note,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.provenanceEvents.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
