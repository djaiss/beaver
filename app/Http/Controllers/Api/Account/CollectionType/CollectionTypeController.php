<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CollectionType;

use App\Actions\CreateCollectionType;
use App\Actions\DestroyCollectionType;
use App\Actions\UpdateCollectionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CollectionTypeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $types = $account->collectionTypes()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return CollectionTypeResource::collection($types);
    }

    public function show(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);

        return new CollectionTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $type = new CreateCollectionType(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            color: $validated['color'] ?? '#6B7280',
        )->execute();

        return new CollectionTypeResource($type)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $type = new UpdateCollectionType(
            user: $request->user(),
            collectionType: $type,
            name: $validated['name'],
            color: $validated['color'],
        )->execute();

        return new CollectionTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);

        new DestroyCollectionType(
            user: $request->user(),
            collectionType: $type,
        )->execute();

        return response()->noContent(204);
    }
}
