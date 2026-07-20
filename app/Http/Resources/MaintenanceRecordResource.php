<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MaintenanceRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MaintenanceRecord
 */
class MaintenanceRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'maintenance_record',
            'id' => (string) $this->id,
            'attributes' => [
                'copy_id' => (string) $this->copy_id,
                'provenance_event_id' => $this->provenance_event_id === null ? null : (string) $this->provenance_event_id,
                'type' => $this->type->value,
                'title' => $this->title,
                'description' => $this->description,
                'performed_by' => $this->performed_by,
                'performed_at' => $this->performed_at?->timestamp,
                'cost_amount' => $this->cost_amount,
                'cost_currency_code' => $this->cost_currency_code,
                'condition_before_id' => $this->condition_before_id === null ? null : (string) $this->condition_before_id,
                'condition_after_id' => $this->condition_after_id === null ? null : (string) $this->condition_after_id,
                'next_due_at' => $this->next_due_at?->timestamp,
                'include_in_provenance' => $this->include_in_provenance,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.copies.maintenanceRecords.show', [$this->copy_id, $this->id]),
            ],
        ];
    }
}
