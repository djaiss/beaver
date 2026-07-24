<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\DestroyDocument;
use App\Actions\UpdateDocument;
use App\Actions\UploadDocument;
use App\Enums\DocumentType;
use App\Http\Controllers\Controller;
use App\Models\Collection as CollectionModel;
use App\Models\Copy;
use App\Models\Document;
use App\Models\Item;
use App\Traits\ResolvesDocumentables;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The documents attached to a copy, or to one of the records on it, managed from
 * the history tab of its item.
 *
 * A document is either an uploaded file or a link to one held elsewhere. The
 * upload lives under the copy so the account and the item are always in context,
 * while the record it actually hangs from is named in the request and resolved
 * within that copy.
 */
class DocumentController extends Controller
{
    use ResolvesDocumentables;

    public function create(Request $request, CollectionModel $collection, Item $item, Copy $copy): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $documentable = $this->findDocumentable($copy, $validated['documentable_type'], (int) $validated['documentable_id']);

        new UploadDocument(
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

        return to_route('items.history.show', [$collection, $item, $copy, $this->sectionForDocumentable($validated['documentable_type'])])
            ->with('status', __('Document attached'))
            ->with('status_description', __('The document was added to the history of this copy.'));
    }

    public function update(Request $request, CollectionModel $collection, Item $item, Copy $copy, int $document): RedirectResponse
    {
        $documentModel = $this->findDocument($request, $document);

        $validated = $request->validate($this->metadataRules());

        new UpdateDocument(
            user: $request->user(),
            document: $documentModel,
            type: DocumentType::from($validated['type']),
            name: $validated['name'],
            description: $validated['description'] ?? null,
            issuedAt: $validated['issued_at'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collection, $item, $copy, $this->sectionForDocumentable($documentModel->documentable_type)])
            ->with('status', __('Document updated'))
            ->with('status_description', __('Your changes to the document were saved.'));
    }

    public function destroy(Request $request, CollectionModel $collection, Item $item, Copy $copy, int $document): RedirectResponse
    {
        $documentModel = $this->findDocument($request, $document);

        $section = $this->sectionForDocumentable($documentModel->documentable_type);

        new DestroyDocument(
            user: $request->user(),
            document: $documentModel,
        )->execute();

        return to_route('items.history.show', [$collection, $item, $copy, $section])
            ->with('status', __('Document deleted'))
            ->with('status_description', __('The document and its file were removed.'));
    }

    private function findDocument(Request $request, int $document): Document
    {
        try {
            return Document::query()->ofAccount($request->user()->account)->findOrFail($document);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * @return array<string, list<mixed>|string>
     */
    private function rules(): array
    {
        return [
            'documentable_type' => ['required', 'string', Rule::in(['copy', ...array_keys($this->documentableRelations())])],
            'documentable_id' => ['required', 'integer'],
            'file' => ['nullable', 'required_without:external_url', 'file', 'max:'.(int) config('documents.max_size_in_kilobytes'), 'mimetypes:'.implode(',', config('documents.allowed_mime_types'))],
            'external_url' => ['nullable', 'required_without:file', 'url', 'max:2000'],
            ...$this->metadataRules(),
        ];
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
