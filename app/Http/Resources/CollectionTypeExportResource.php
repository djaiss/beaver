<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\CollectionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CollectionType
 */
class CollectionTypeExportResource extends JsonResource
{
    /**
     * The schema is built by the ExportCollectionType action rather than read
     * off the model, so it comes in alongside it.
     *
     * @param  array<string, mixed>  $schema
     */
    public function __construct(CollectionType $resource, private array $schema)
    {
        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'collection_type_export',
            'id' => (string) $this->id,
            'attributes' => [
                // Kept verbatim, camelCase keys and all: this is the portable
                // document the web export page hands out, not a view of it.
                'schema' => $this->schema,
            ],
            'links' => [
                'self' => route('api.collectionTypes.export.show', $this->id),
                'collection_type' => route('api.collectionTypes.show', $this->id),
            ],
        ];
    }
}
