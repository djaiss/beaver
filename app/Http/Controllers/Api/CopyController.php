<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateCopy;
use App\Actions\DestroyCopy;
use App\Actions\UpdateCopy;
use App\Http\Controllers\Controller;
use App\Http\Resources\CopyResource;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CopyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $item = $this->findItem($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $copies = $item->copies()
            ->orderBy('id')
            ->paginate($perPage);

        return CopyResource::collection($copies);
    }

    public function show(Request $request): JsonResponse
    {
        $item = $this->findItem($request);
        $copyId = $request->route()->parameter('copy');

        $copy = $item->copies()->findOrFail($copyId);

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $item = $this->findItem($request);

        $validated = $this->validatePayload($request);

        $account = $request->user()->account;

        $copy = new CreateCopy(
            user: $request->user(),
            item: $item,
            condition: isset($validated['condition_id']) ? $account->conditions()->find($validated['condition_id']) : null,
            location: isset($validated['location_id']) ? $account->locations()->find($validated['location_id']) : null,
            acquiredAt: $validated['acquired_at'] ?? null,
            pricePaid: $validated['price_paid'] ?? null,
            estimatedValue: $validated['estimated_value'] ?? null,
        )->execute();

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $item = $this->findItem($request);
        $copyId = $request->route()->parameter('copy');

        $copy = $item->copies()->findOrFail($copyId);

        $validated = $this->validatePayload($request);

        $account = $request->user()->account;

        $copy = new UpdateCopy(
            user: $request->user(),
            copy: $copy,
            condition: isset($validated['condition_id']) ? $account->conditions()->find($validated['condition_id']) : null,
            location: isset($validated['location_id']) ? $account->locations()->find($validated['location_id']) : null,
            acquiredAt: $validated['acquired_at'] ?? null,
            pricePaid: $validated['price_paid'] ?? null,
            estimatedValue: $validated['estimated_value'] ?? null,
        )->execute();

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $item = $this->findItem($request);
        $copyId = $request->route()->parameter('copy');

        $copy = $item->copies()->findOrFail($copyId);

        new DestroyCopy(
            user: $request->user(),
            copy: $copy,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'condition_id' => ['nullable', 'integer'],
            'location_id' => ['nullable', 'integer'],
            'acquired_at' => ['nullable', 'date'],
            'price_paid' => ['nullable', 'integer', 'min:0'],
            'estimated_value' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function findItem(Request $request): Item
    {
        $itemId = $request->route()->parameter('item');
        $account = $request->user()->account;

        return Item::query()
            ->whereHas('collection', fn ($query) => $query->whereBelongsTo($account))
            ->findOrFail($itemId);
    }
}
