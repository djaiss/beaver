<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The payload carries both totals. The stored one is optional, and the derived
 * one falls back to the sum of the parts, so a client that wants what actually
 * changed hands reads `total` and never has to decide between the two.
 *
 * @mixin Transaction
 */
class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'transaction',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'type' => $this->type->value,
                'counterparty' => $this->counterparty,
                'amount' => $this->amount,
                'currency_code' => $this->currency_code,
                'tax_amount' => $this->tax_amount,
                'fee_amount' => $this->fee_amount,
                'shipping_amount' => $this->shipping_amount,
                'total_amount' => $this->total_amount,
                'total' => $this->total(),
                'occurred_at' => $this->occurred_at->timestamp,
                'reference_number' => $this->reference_number,
                'source_url' => $this->source_url,
                'note' => $this->note,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.transactions.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
