<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CatalogType;

use App\Actions\CreateCatalogType;
use App\Actions\DestroyCatalogType;
use App\Actions\UpdateCatalogType;
use App\Http\Controllers\Controller;
use App\Http\Resources\CatalogTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CatalogTypeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $types = $account->catalogTypes()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return CatalogTypeResource::collection($types);
    }

    public function show(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        return new CatalogTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $type = new CreateCatalogType(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
            color: $validated['color'] ?? '#6B7280',
        )->execute();

        return new CatalogTypeResource($type)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $type = new UpdateCatalogType(
            user: $request->user(),
            catalogType: $type,
            name: $validated['name'],
            color: $validated['color'],
        )->execute();

        return new CatalogTypeResource($type)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $catalogTypeId = $request->route()->parameter('collectionType');

        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        new DestroyCatalogType(
            user: $request->user(),
            catalogType: $type,
        )->execute();

        return response()->noContent(204);
    }
}
