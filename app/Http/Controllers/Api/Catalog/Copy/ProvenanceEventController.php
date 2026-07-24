<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog\Copy;

use App\Actions\CreateProvenanceEvent;
use App\Actions\DestroyProvenanceEvent;
use App\Actions\UpdateProvenanceEvent;
use App\Enums\DatePrecision;
use App\Enums\ProvenanceEventType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProvenanceEventResource;
use App\Models\Copy;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ProvenanceEventController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $events = $copy->provenanceEvents()->paginate($perPage);

        return ProvenanceEventResource::collection($events);
    }

    public function show(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $eventId = $request->route()->parameter('provenanceEvent');
        $event = $copy->provenanceEvents()->findOrFail($eventId);

        return new ProvenanceEventResource($event)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(ProvenanceEventType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'occurred_at' => ['nullable', 'date'],
            'occurred_at_precision' => ['nullable', Rule::enum(DatePrecision::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'from_party' => ['nullable', 'string', 'max:255'],
            'to_party' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            'is_verified' => ['nullable', 'boolean'],
            'verification_note' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'integer'],
        ]);

        $event = new CreateProvenanceEvent(
            user: $request->user(),
            copy: $copy,
            type: ProvenanceEventType::from($validated['type']),
            title: $validated['title'],
            occurredAtPrecision: DatePrecision::from($validated['occurred_at_precision'] ?? DatePrecision::Exact->value),
            occurredAt: $validated['occurred_at'] ?? null,
            description: $validated['description'] ?? null,
            location: $validated['location'] ?? null,
            fromParty: $validated['from_party'] ?? null,
            toParty: $validated['to_party'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            isVerified: (bool) ($validated['is_verified'] ?? false),
            verificationNote: $validated['verification_note'] ?? null,
            transaction: $this->findTransaction($copy, $validated['transaction_id'] ?? null),
        )->execute();

        return new ProvenanceEventResource($event)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $eventId = $request->route()->parameter('provenanceEvent');
        $event = $copy->provenanceEvents()->findOrFail($eventId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(ProvenanceEventType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'occurred_at' => ['nullable', 'date'],
            'occurred_at_precision' => ['nullable', Rule::enum(DatePrecision::class)],
            'location' => ['nullable', 'string', 'max:255'],
            'from_party' => ['nullable', 'string', 'max:255'],
            'to_party' => ['nullable', 'string', 'max:255'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            'is_verified' => ['nullable', 'boolean'],
            'verification_note' => ['nullable', 'string'],
            'transaction_id' => ['nullable', 'integer'],
        ]);

        $event = new UpdateProvenanceEvent(
            user: $request->user(),
            event: $event,
            type: ProvenanceEventType::from($validated['type']),
            title: $validated['title'],
            occurredAtPrecision: DatePrecision::from($validated['occurred_at_precision'] ?? DatePrecision::Exact->value),
            occurredAt: $validated['occurred_at'] ?? null,
            description: $validated['description'] ?? null,
            location: $validated['location'] ?? null,
            fromParty: $validated['from_party'] ?? null,
            toParty: $validated['to_party'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            isVerified: (bool) ($validated['is_verified'] ?? false),
            verificationNote: $validated['verification_note'] ?? null,
            transaction: $this->findTransaction($event->copy, $validated['transaction_id'] ?? null),
        )->execute();

        return new ProvenanceEventResource($event)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $eventId = $request->route()->parameter('provenanceEvent');
        $event = $copy->provenanceEvents()->findOrFail($eventId);

        new DestroyProvenanceEvent(
            user: $request->user(),
            event: $event,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * Resolve the transaction the payload points at through the copy itself, so
     * a foreign id never reaches the action.
     */
    private function findTransaction(Copy $copy, ?int $transactionId): ?Transaction
    {
        if ($transactionId === null) {
            return null;
        }

        return $copy->transactions()->findOrFail($transactionId);
    }
}
