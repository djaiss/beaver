<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Valuation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Valuation
 */
class ValuationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'valuation',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'type' => $this->type->value,
                'amount' => $this->amount,
                'currency_code' => $this->currency_code,
                'valued_at' => $this->valued_at->timestamp,
                'confidence' => $this->confidence->value,
                'valuer' => $this->valuer,
                'method' => $this->method,
                'source_url' => $this->source_url,
                'reference_number' => $this->reference_number,
                'note' => $this->note,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.valuations.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
