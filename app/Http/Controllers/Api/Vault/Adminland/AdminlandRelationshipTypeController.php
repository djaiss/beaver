<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vault\Adminland;

use App\Actions\CreateRelationshipType;
use App\Actions\DestroyRelationshipType;
use App\Actions\UpdateRelationshipType;
use App\Http\Controllers\Controller;
use App\Http\Resources\RelationshipTypeResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminlandRelationshipTypeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');
        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $relationshipTypes = $relationshipTypeCategory
            ->relationshipTypes()
            ->orderBy('position')
            ->paginate($perPage);

        return RelationshipTypeResource::collection($relationshipTypes);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->findOrFail($request->route()->parameter('relationshipType'));

        return new RelationshipTypeResource($relationshipType)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'is_directed' => ['sometimes', 'boolean'],
            'forward_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
            'reverse_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
        ]);

        $relationshipType = new CreateRelationshipType(
            user: $request->user(),
            vault: $vault,
            relationshipTypeCategory: $relationshipTypeCategory,
            key: null,
            name: $validated['name'],
            isDirected: (bool) ($validated['is_directed'] ?? false),
            forwardName: $validated['forward_name'] ?? null,
            reverseName: $validated['reverse_name'] ?? null,
        )->execute();

        return new RelationshipTypeResource($relationshipType)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->findOrFail($request->route()->parameter('relationshipType'));

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'is_directed' => ['sometimes', 'boolean'],
            'forward_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
            'reverse_name' => ['required_if_accepted:is_directed', 'nullable', 'string', 'min:3', 'max:100'],
            'position' => ['sometimes', 'integer', 'min:1'],
        ]);
        $isDirected = (bool) ($validated['is_directed'] ?? $relationshipType->is_directed);

        $relationshipType = new UpdateRelationshipType(
            user: $request->user(),
            relationshipType: $relationshipType,
            name: $validated['name'],
            isDirected: $isDirected,
            position: isset($validated['position']) ? (int) $validated['position'] : $relationshipType->position,
            forwardName: $validated['forward_name'] ?? null,
            reverseName: $validated['reverse_name'] ?? null,
        )->execute();

        return new RelationshipTypeResource($relationshipType)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('relationshipTypeCategory');

        $relationshipTypeCategory = $vault->relationshipTypeCategories()->findOrFail($id);

        $relationshipType = $relationshipTypeCategory
            ->relationshipTypes()
            ->findOrFail($request->route()->parameter('relationshipType'));

        new DestroyRelationshipType(
            user: $request->user(),
            relationshipType: $relationshipType,
        )->execute();

        return response()->noContent(204);
    }
}
