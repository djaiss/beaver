<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog\Copy;

use App\Actions\DestroyDocument;
use App\Actions\UpdateDocument;
use App\Actions\UploadDocument;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Http\Resources\DocumentResource;
use App\Models\Copy;
use App\Models\Document;
use App\Traits\ResolvesDocumentables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

/**
 * The documents attached to a copy and every record on it. Documents are
 * polymorphic, so this one endpoint lists, adds and removes documents for the
 * copy itself and for its transactions, provenance events, valuations, insurance
 * records, maintenance records and loans, keyed by the documentable it hangs
 * from.
 */
class DocumentController extends Controller
{
    use ResolvesDocumentables;

    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $documents = $this->documentsForCopy($copy)
            ->orderByDesc('id')
            ->paginate($perPage);

        return DocumentResource::collection($documents);
    }

    public function show(Request $request): JsonResponse
    {
        $account = $request->user()->account;
        $documentId = $request->route()->parameter('document');
        $document = Document::query()->ofAccount($account)->findOrFail($documentId);

        return new DocumentResource($document)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'documentable_type' => ['nullable', 'string', Rule::in(['copy', ...array_keys($this->documentableRelations())])],
            'documentable_id' => ['nullable', 'integer'],
            'file' => ['nullable', 'required_without:external_url', 'file', 'max:'.(int) config('documents.max_size_in_kilobytes'), 'mimetypes:'.implode(',', config('documents.allowed_mime_types'))],
            'external_url' => ['nullable', 'required_without:file', 'url', 'max:2000'],
            ...$this->metadataRules(),
        ]);

        $documentable = $this->findDocumentable(
            $copy,
            $validated['documentable_type'] ?? 'copy',
            (int) ($validated['documentable_id'] ?? $copy->id),
        );

        $document = new UploadDocument(
            user: $request->user(),
            copy: $copy,
            documentable: $documentable,
            type: DocumentType::from($validated['type']),
            name: $validated['name'],
            file: $request->file('file'),
            externalUrl: $validated['external_url'] ?? null,
            description: $validated['description'] ?? null,
            issuedAt: $validated['issued_at'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
        )->execute();

        return new DocumentResource($document)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $account = $request->user()->account;
        $documentId = $request->route()->parameter('document');
        $document = Document::query()->ofAccount($account)->findOrFail($documentId);

        $validated = $request->validate($this->metadataRules());

        $document = new UpdateDocument(
            user: $request->user(),
            document: $document,
            type: DocumentType::from($validated['type']),
            name: $validated['name'],
            description: $validated['description'] ?? null,
            issuedAt: $validated['issued_at'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
        )->execute();

        return new DocumentResource($document)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $account = $request->user()->account;
        $documentId = $request->route()->parameter('document');
        $document = Document::query()->ofAccount($account)->findOrFail($documentId);

        new DestroyDocument(
            user: $request->user(),
            document: $document,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * The documents of a copy: the ones on the copy itself, and the ones on every
     * record hanging off it, in one scope so the copy's whole paper trail reads
     * together.
     *
     * @return Builder<Document>
     */
    private function documentsForCopy(Copy $copy): Builder
    {
        return Document::query()
            ->ofAccount($copy->item->catalog->account)
            ->where(function (Builder $query) use ($copy): void {
                $query->where(fn (Builder $inner) => $inner
                    ->where('documentable_type', 'copy')
                    ->where('documentable_id', $copy->id));

                foreach ($this->documentableRelations() as $type => $relation) {
                    $ids = $copy->{$relation}()->pluck('id');

                    if ($ids->isEmpty()) {
                        continue;
                    }

                    $query->orWhere(fn (Builder $inner) => $inner
                        ->where('documentable_type', $type)
                        ->whereIn('documentable_id', $ids));
                }
            });
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function metadataRules(): array
    {
        return [
            'type' => ['required', Rule::enum(DocumentType::class)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'issued_at' => ['nullable', 'date'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}
