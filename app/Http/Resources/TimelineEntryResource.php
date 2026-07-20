<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\ValueObjects\TimelineEntry;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin TimelineEntry
 */
class TimelineEntryResource extends JsonResource
{
    /**
     * Transform a timeline entry into an array.
     *
     * An entry is a read model rather than a stored row, so it has no url of its
     * own. Its identity is the source it came from and that row's id, and the
     * source and source_id point a caller at the record to read for the full
     * detail. The amount stays in its own currency and is never converted.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'timeline_entry',
            'id' => $this->resource->key(),
            'attributes' => [
                'source_type' => $this->resource->source->value,
                'source_id' => (string) $this->resource->sourceId,
                'title' => $this->resource->title,
                'summary' => $this->resource->summary,
                'date' => $this->resource->date?->timestamp,
                'date_precision' => $this->resource->precision->value,
                'amount' => $this->resource->amountCents,
                'currency_code' => $this->resource->currencyCode,
                'meaningful' => $this->resource->meaningful,
            ],
        ];
    }
}
