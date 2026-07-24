<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\CreateItem;
use App\Actions\DestroyItem;
use App\Actions\UpdateItem;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ItemController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $catalogId = $request->route()->parameter('collection');
        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $items = $catalog->items()
            ->orderBy('id')
            ->paginate($perPage);

        return ItemResource::collection($items);
    }

    public function show(Request $request): JsonResponse
    {
        $catalogId = $request->route()->parameter('collection');
        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);
        $itemId = $request->route()->parameter('item');

        $item = $catalog->items()->findOrFail($itemId);

        return new ItemResource($item)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $catalogId = $request->route()->parameter('collection');
        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type_id' => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'set_id' => ['nullable', 'integer'],
            'series_id' => ['nullable', 'integer'],
        ]);

        $account = $request->user()->account;

        $item = new CreateItem(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            catalogType: isset($validated['type_id']) ? $account->catalogTypes()->find($validated['type_id']) : null,
            category: isset($validated['category_id']) ? $catalog->categories()->find($validated['category_id']) : null,
            set: isset($validated['set_id']) ? $catalog->sets()->find($validated['set_id']) : null,
            series: isset($validated['series_id']) ? $account->series()->find($validated['series_id']) : null,
        )->execute();

        return new ItemResource($item)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $catalogId = $request->route()->parameter('collection');
        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);
        $itemId = $request->route()->parameter('item');

        $item = $catalog->items()->findOrFail($itemId);

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
            catalogType: isset($validated['type_id']) ? $account->catalogTypes()->find($validated['type_id']) : null,
            // The API does not manage the catalog placement, so the current
            // category and set are passed back to leave them where they are.
            category: $item->category,
            set: $item->set,
            series: $item->series,
        )->execute();

        return new ItemResource($item)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $catalogId = $request->route()->parameter('collection');
        $catalog = $request->user()->account->catalogs()->findOrFail($catalogId);
        $itemId = $request->route()->parameter('item');

        $item = $catalog->items()->findOrFail($itemId);

        new DestroyItem(
            user: $request->user(),
            item: $item,
        )->execute();

        return response()->noContent(204);
    }
}
