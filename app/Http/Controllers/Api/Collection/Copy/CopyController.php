<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection\Copy;

use App\Actions\CreateCopy;
use App\Actions\DestroyCopy;
use App\Actions\UpdateCopy;
use App\Enums\CopyStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\CopyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CopyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $itemId = $request->route()->parameter('item');
        $item = $account->items()->findOrFail($itemId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        // The resource reads the estimated value off the latest valuation, so
        // without this the listing fires one more query per copy.
        $copies = $item->copies()
            ->with('latestValuation')
            ->orderBy('id')
            ->paginate($perPage);

        return CopyResource::collection($copies);
    }

    public function show(Request $request): JsonResponse
    {
        $account = $request->user()->account;

        $itemId = $request->route()->parameter('item');
        $item = $account->items()->findOrFail($itemId);

        $copyId = $request->route()->parameter('copy');
        $copy = $item->copies()->findOrFail($copyId);

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $account = $request->user()->account;

        $itemId = $request->route()->parameter('item');
        $item = $account->items()->findOrFail($itemId);

        // The estimated value is accepted but never stored on the copy: the
        // action turns it into a valuation.
        $validated = $request->validate([
            'identifier' => ['nullable', 'string', 'max:255'],
            'item_condition_id' => ['nullable', 'integer'],
            'location_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::enum(CopyStatus::class)],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'disposed_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'estimated_value' => ['nullable', 'integer', 'min:0'],
        ]);

        $copy = new CreateCopy(
            user: $request->user(),
            item: $item,
            itemCondition: isset($validated['item_condition_id']) ? $account->itemConditions()->find($validated['item_condition_id']) : null,
            location: isset($validated['location_id']) ? $account->locations()->find($validated['location_id']) : null,
            identifier: $validated['identifier'] ?? null,
            status: isset($validated['status']) ? CopyStatus::from($validated['status']) : CopyStatus::Owned,
            quantity: $validated['quantity'] ?? 1,
            disposedAt: $validated['disposed_at'] ?? null,
            note: $validated['note'] ?? null,
            estimatedValue: $validated['estimated_value'] ?? null,
        )->execute();

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $account = $request->user()->account;

        $itemId = $request->route()->parameter('item');
        $item = $account->items()->findOrFail($itemId);

        $copyId = $request->route()->parameter('copy');
        $copy = $item->copies()->findOrFail($copyId);

        // The estimated value is accepted but never stored on the copy: the
        // action turns it into a valuation.
        $validated = $request->validate([
            'identifier' => ['nullable', 'string', 'max:255'],
            'item_condition_id' => ['nullable', 'integer'],
            'location_id' => ['nullable', 'integer'],
            'status' => ['nullable', Rule::enum(CopyStatus::class)],
            'quantity' => ['nullable', 'integer', 'min:1'],
            'disposed_at' => ['nullable', 'date'],
            'note' => ['nullable', 'string'],
            'estimated_value' => ['nullable', 'integer', 'min:0'],
        ]);

        $copy = new UpdateCopy(
            user: $request->user(),
            copy: $copy,
            itemCondition: isset($validated['item_condition_id']) ? $account->itemConditions()->find($validated['item_condition_id']) : null,
            location: isset($validated['location_id']) ? $account->locations()->find($validated['location_id']) : null,
            identifier: $validated['identifier'] ?? null,
            status: isset($validated['status']) ? CopyStatus::from($validated['status']) : CopyStatus::Owned,
            quantity: $validated['quantity'] ?? 1,
            disposedAt: $validated['disposed_at'] ?? null,
            note: $validated['note'] ?? null,
            estimatedValue: $validated['estimated_value'] ?? null,
        )->execute();

        return new CopyResource($copy)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $account = $request->user()->account;

        $itemId = $request->route()->parameter('item');
        $item = $account->items()->findOrFail($itemId);

        $copyId = $request->route()->parameter('copy');
        $copy = $item->copies()->findOrFail($copyId);

        new DestroyCopy(
            user: $request->user(),
            copy: $copy,
        )->execute();

        return response()->noContent(204);
    }
}
