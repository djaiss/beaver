<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateValuation;
use App\Actions\DestroyValuation;
use App\Actions\UpdateValuation;
use App\Enums\ValuationConfidence;
use App\Enums\ValuationType;
use App\Http\Controllers\Controller;
use App\Http\Resources\ValuationResource;
use App\Models\Copy;
use App\Models\Valuation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ValuationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copy = $this->findCopy($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $valuations = $copy->valuations()->paginate($perPage);

        return ValuationResource::collection($valuations);
    }

    public function show(Request $request): JsonResponse
    {
        $valuation = $this->findValuation($request);

        return new ValuationResource($valuation)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copy = $this->findCopy($request);

        $validated = $this->validatePayload($request);

        $valuation = new CreateValuation(
            user: $request->user(),
            copy: $copy,
            type: ValuationType::from($validated['type']),
            amount: $validated['amount'],
            valuedAt: $validated['valued_at'],
            currencyCode: $validated['currency_code'] ?? null,
            confidence: ValuationConfidence::from($validated['confidence'] ?? ValuationConfidence::Unknown->value),
            valuer: $validated['valuer'] ?? null,
            method: $validated['method'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new ValuationResource($valuation)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $valuation = $this->findValuation($request);

        $validated = $this->validatePayload($request);

        $valuation = new UpdateValuation(
            user: $request->user(),
            valuation: $valuation,
            type: ValuationType::from($validated['type']),
            amount: $validated['amount'],
            valuedAt: $validated['valued_at'],
            currencyCode: $validated['currency_code'] ?? null,
            confidence: ValuationConfidence::from($validated['confidence'] ?? ValuationConfidence::Unknown->value),
            valuer: $validated['valuer'] ?? null,
            method: $validated['method'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new ValuationResource($valuation)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $valuation = $this->findValuation($request);

        new DestroyValuation(
            user: $request->user(),
            valuation: $valuation,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'type' => ['required', Rule::enum(ValuationType::class)],
            'amount' => ['required', 'integer', 'min:0'],
            'valued_at' => ['required', 'date'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'confidence' => ['nullable', Rule::enum(ValuationConfidence::class)],
            'valuer' => ['nullable', 'string', 'max:255'],
            'method' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);
    }

    private function findCopy(Request $request): Copy
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;

        return Copy::query()
            ->whereHas('item.collection', fn ($query) => $query->whereBelongsTo($account))
            ->findOrFail($copyId);
    }

    private function findValuation(Request $request): Valuation
    {
        $copy = $this->findCopy($request);
        $valuationId = $request->route()->parameter('valuation');

        return $copy->valuations()->findOrFail($valuationId);
    }
}
