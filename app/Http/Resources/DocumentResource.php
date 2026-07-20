<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Document
 */
class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * The stored disk path is never exposed. A stored document is reached through
     * the streamed download URL instead, and an external one through its link.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'document',
            'id' => (string) $this->id,
            'attributes' => [
                'documentable_type' => $this->documentable_type,
                'documentable_id' => (string) $this->documentable_id,
                'document_type' => $this->type->value,
                'name' => $this->name,
                'external_url' => $this->external_url,
                'download_url' => $this->isStored() ? route('documents.show', $this->resource) : null,
                'mime_type' => $this->mime_type,
                'size' => $this->size,
                'description' => $this->description,
                'issued_at' => $this->issued_at?->timestamp,
                'reference_number' => $this->reference_number,
                'created_at' => $this->created_at->timestamp,
                'updated_at' => $this->updated_at?->timestamp,
            ],
            'links' => [
                'self' => route('api.documents.show', $this->id),
            ],
        ];
    }
}
