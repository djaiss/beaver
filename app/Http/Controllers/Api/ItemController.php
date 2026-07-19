<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateItem;
use App\Actions\DestroyItem;
use App\Actions\UpdateItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $collection = $this->findCollection($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $items = $collection->items()
            ->orderBy('id')
            ->paginate($perPage);

        return ItemResource::collection($items);
    }

    public function show(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);
        $itemId = $request->route()->parameter('item');

        $item = $collection->items()->findOrFail($itemId);

        return new ItemResource($item)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'set_id' => ['nullable', 'integer'],
        ]);

        $account = $request->user()->account;

        $item = new CreateItem(
            user: $request->user(),
            collection: $collection,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            collectionType: isset($validated['type_id']) ? $account->collectionTypes()->find($validated['type_id']) : null,
            category: isset($validated['category_id']) ? $collection->categories()->find($validated['category_id']) : null,
            set: isset($validated['set_id']) ? $collection->sets()->find($validated['set_id']) : null,
        )->execute();

        return new ItemResource($item)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);
        $itemId = $request->route()->parameter('item');

        $item = $collection->items()->findOrFail($itemId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type_id' => ['nullable', 'integer'],
        ]);

        $account = $request->user()->account;

        $item = new UpdateItem(
            user: $request->user(),
            item: $item,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            collectionType: isset($validated['type_id']) ? $account->collectionTypes()->find($validated['type_id']) : null,
            // The API does not manage the catalog placement, so the current
            // category and set are passed back to leave them where they are.
            category: $item->category,
            set: $item->set,
        )->execute();

        return new ItemResource($item)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $collection = $this->findCollection($request);
        $itemId = $request->route()->parameter('item');

        $item = $collection->items()->findOrFail($itemId);

        new DestroyItem(
            user: $request->user(),
            item: $item,
        )->execute();

        return response()->noContent(204);
    }

    private function findCollection(Request $request): Collection
    {
        $collectionId = $request->route()->parameter('collection');

        return $request->user()->account->collections()->findOrFail($collectionId);
    }
}
