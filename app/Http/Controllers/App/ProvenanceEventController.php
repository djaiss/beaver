<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateProvenanceEvent;
use App\Actions\DestroyProvenanceEvent;
use App\Actions\UpdateProvenanceEvent;
use App\Enums\DatePrecision;
use App\Enums\ProvenanceEventType;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Copy;
use App\Models\Item;
use App\Models\ProvenanceEvent;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The provenance of a copy, recorded from the history tab of its item.
 *
 * No amount is collected here. What a moment cost belongs to a transaction, so
 * an event that came from an exchange points at one rather than restating it.
 */
class ProvenanceEventController extends Controller
{
    public function create(Request $request, Catalog $catalog, Item $item, Copy $copy): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        new CreateProvenanceEvent(
            user: $request->user(),
            copy: $copy,
            type: ProvenanceEventType::from($validated['type']),
            title: $validated['title'],
            occurredAtPrecision: DatePrecision::from($validated['occurred_at_precision']),
            occurredAt: $validated['occurred_at'] ?? null,
            description: $validated['description'] ?? null,
            location: $validated['location'] ?? null,
            fromParty: $validated['from_party'] ?? null,
            toParty: $validated['to_party'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            isVerified: (bool) ($validated['is_verified'] ?? false),
            verificationNote: $validated['verification_note'] ?? null,
            transaction: $this->findLinkedTransaction($copy, $validated['transaction_id'] ?? null),
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'provenance'])
            ->with('status', __('Provenance event recorded'))
            ->with('status_description', __('The event was added to the story of this copy.'));
    }

    public function update(Request $request, Catalog $catalog, Item $item, Copy $copy, int $provenanceEvent): RedirectResponse
    {
        $eventModel = $this->findProvenanceEvent($copy, $provenanceEvent);

        $validated = $request->validate($this->rules());

        new UpdateProvenanceEvent(
            user: $request->user(),
            event: $eventModel,
            type: ProvenanceEventType::from($validated['type']),
            title: $validated['title'],
            occurredAtPrecision: DatePrecision::from($validated['occurred_at_precision']),
            occurredAt: $validated['occurred_at'] ?? null,
            description: $validated['description'] ?? null,
            location: $validated['location'] ?? null,
            fromParty: $validated['from_party'] ?? null,
            toParty: $validated['to_party'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            isVerified: (bool) ($validated['is_verified'] ?? false),
            verificationNote: $validated['verification_note'] ?? null,
            transaction: $this->findLinkedTransaction($copy, $validated['transaction_id'] ?? null),
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'provenance'])
            ->with('status', __('Provenance event updated'))
            ->with('status_description', __('Your changes to the event were saved.'));
    }

    public function destroy(Request $request, Catalog $catalog, Item $item, Copy $copy, int $provenanceEvent): RedirectResponse
    {
        $eventModel = $this->findProvenanceEvent($copy, $provenanceEvent);

        new DestroyProvenanceEvent(
            user: $request->user(),
            event: $eventModel,
        )->execute();

        return to_route('items.history.show', [$catalog, $item, $copy, 'provenance'])
            ->with('status', __('Provenance event deleted'))
            ->with('status_description', __('The event was removed from the story of this copy.'));
    }

    private function findProvenanceEvent(Copy $copy, int $provenanceEvent): ProvenanceEvent
    {
        try {
            return $copy->provenanceEvents()->findOrFail($provenanceEvent);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * Resolve the transaction the form pointed at, which has to be one of this
     * copy's own. Linking across copies would attach the money of one object to
     * the story of another.
     */
    private function findLinkedTransaction(Copy $copy, mixed $transactionId): ?Transaction
    {
        if ($transactionId === null || $transactionId === '') {
            return null;
        }

        try {
            return $copy->transactions()->findOrFail((int) $transactionId);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(ProvenanceEventType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'occurred_at' => ['nullable', 'date'],
            'occurred_at_precision' => ['required', Rule::enum(DatePrecision::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'from_party' => ['nullable', 'string', 'max:255'],
            'to_party' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2000'],
            'is_verified' => ['nullable', 'boolean'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
            'transaction_id' => ['nullable', 'integer'],
        ];
    }
}
