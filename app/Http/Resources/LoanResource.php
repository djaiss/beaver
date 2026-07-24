<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Loan
 */
class LoanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'loan',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'loan_provenance_event_id' => $this->loan_provenance_event_id === null ? null : (string) $this->loan_provenance_event_id,
                'return_provenance_event_id' => $this->return_provenance_event_id === null ? null : (string) $this->return_provenance_event_id,
                'direction' => $this->direction->value,
                'status' => $this->status->value,
                'party' => $this->party,
                'purpose' => $this->purpose,
                'loaned_at' => $this->loaned_at->timestamp,
                'due_at' => $this->due_at?->timestamp,
                'returned_at' => $this->returned_at?->timestamp,
                'item_condition_out_id' => $this->item_condition_out_id === null ? null : (string) $this->item_condition_out_id,
                'item_condition_in_id' => $this->item_condition_in_id === null ? null : (string) $this->item_condition_in_id,
                'deposit_amount' => $this->deposit_amount,
                'deposit_currency_code' => $this->deposit_currency_code,
                'include_in_provenance' => $this->include_in_provenance,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            // The object a loan is about, filled in only when the caller asked for
            // the account-wide list and the relations were eager loaded.
            'context' => [
                'item_name' => $this->relationLoaded('copy') && $this->copy->relationLoaded('item') ? $this->copy->item->name : null,
                'copy_identifier' => $this->relationLoaded('copy') ? $this->copy->identifier : null,
                'collection_name' => $this->relationLoaded('copy') && $this->copy->relationLoaded('item') && $this->copy->item->relationLoaded('catalog') ? $this->copy->item->catalog->name : null,
            ],
            'links' => [
                'self' => route('api.copies.loans.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
