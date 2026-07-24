<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog;

use App\Actions\CreateSet;
use App\Actions\DestroySet;
use App\Actions\UpdateSet;
use App\Http\Controllers\Controller;
use App\Http\Resources\SetResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class SetController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $account = $request->user()->account;

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $sets = $account->sets()
            ->orderBy('id')
            ->paginate($perPage);

        return SetResource::collection($sets);
    }

    public function show(Request $request): JsonResponse
    {
        $setId = $request->route()->parameter('set');

        $set = $request->user()->account->sets()->findOrFail($setId);

        return new SetResource($set)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'catalog_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $catalog = $request->user()->account->catalogs()->findOrFail($validated['catalog_id']);

        $set = new CreateSet(
            user: $request->user(),
            catalog: $catalog,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: $validated['target_count'] ?? null,
        )->execute();

        return new SetResource($set)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $setId = $request->route()->parameter('set');

        $set = $request->user()->account->sets()->findOrFail($setId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'target_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $set = new UpdateSet(
            user: $request->user(),
            set: $set,
            name: $validated['name'],
            description: $validated['description'] ?? null,
            targetCount: $validated['target_count'] ?? null,
        )->execute();

        return new SetResource($set)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $setId = $request->route()->parameter('set');

        $set = $request->user()->account->sets()->findOrFail($setId);

        new DestroySet(
            user: $request->user(),
            set: $set,
        )->execute();

        return response()->noContent(204);
    }
}
