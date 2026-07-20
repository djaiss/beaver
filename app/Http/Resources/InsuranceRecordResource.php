<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\InsuranceRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin InsuranceRecord
 */
class InsuranceRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'insurance_record',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'provider' => $this->provider,
                'policy_number' => $this->policy_number,
                'coverage_type' => $this->coverage_type,
                'insured_value' => $this->insured_value,
                'currency_code' => $this->currency_code,
                'deductible_amount' => $this->deductible_amount,
                'deductible_currency_code' => $this->deductible_currency_code,
                'starts_at' => $this->starts_at?->timestamp,
                'ends_at' => $this->ends_at?->timestamp,
                'status' => $this->status->value,
                'is_scheduled_item' => $this->is_scheduled_item,
                'contact_name' => $this->contact_name,
                'contact_email' => $this->contact_email,
                'contact_phone' => $this->contact_phone,
                'note' => $this->note,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.insuranceRecords.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
