<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Models\Copy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Shared by the document controllers. A document can hang from a copy or from one
 * of the records on it, and the request says which through a morph map alias and
 * an id. Resolving the record through the copy is what keeps it tenant safe: a
 * caller can only ever reach a record that belongs to the copy in the URL, which
 * itself belongs to the caller's account.
 */
trait ResolvesDocumentables
{
    /**
     * The records a document may hang from within a copy, keyed by the morph map
     * alias the request sends. The copy itself is handled separately.
     *
     * @return array<string, string>
     */
    private function documentableRelations(): array
    {
        return [
            'transaction' => 'transactions',
            'provenance_event' => 'provenanceEvents',
            'valuation' => 'valuations',
            'insurance_record' => 'insuranceRecords',
            'maintenance_record' => 'maintenanceRecords',
            'loan' => 'loans',
        ];
    }

    private function findDocumentable(Copy $copy, string $type, int $id): Model
    {
        if ($type === 'copy') {
            if ($copy->id !== $id) {
                abort(404);
            }

            return $copy;
        }

        $relation = $this->documentableRelations()[$type] ?? null;

        if ($relation === null) {
            abort(404);
        }

        try {
            return $copy->{$relation}()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * The history section the document's parent reads under, so an upload or an
     * edit returns to the panel it was made from.
     */
    private function sectionForDocumentable(string $type): string
    {
        return match ($type) {
            'transaction' => 'transactions',
            'valuation' => 'valuations',
            'provenance_event' => 'provenance',
            'insurance_record' => 'insurance',
            'maintenance_record' => 'maintenance',
            'loan' => 'loans',
            default => 'documents',
        };
    }
}
