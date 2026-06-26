<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Vault\Adminland;

use App\Actions\CreateRelationshipTypeCategory;
use App\Actions\DestroyRelationshipTypeCategory;
use App\Actions\UpdateRelationshipTypeCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\RelationshipTypeCategoryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class AdminlandRelationshipTypeCategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vault = $request->attributes->get('vault');
        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $relationshipTypeCategories = $vault
            ->relationshipTypeCategories()
            ->orderBy('position')
            ->paginate($perPage);

        return RelationshipTypeCategoryResource::collection($relationshipTypeCategories);
    }

    public function show(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        $relationshipTypeCategory = $vault
            ->relationshipTypeCategories()
            ->findOrFail($request->route()->parameter('relationshipTypeCategory'));

        return new RelationshipTypeCategoryResource($relationshipTypeCategory)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        $relationshipTypeCategory = new CreateRelationshipTypeCategory(
            user: $request->user(),
            vault: $vault,
            key: null,
            name: $validated['name'],
        )->execute();

        return new RelationshipTypeCategoryResource($relationshipTypeCategory)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $vault = $request->attributes->get('vault');

        $relationshipTypeCategory = $vault
            ->relationshipTypeCategories()
            ->findOrFail($request->route()->parameter('relationshipTypeCategory'));

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'position' => ['sometimes', 'integer', 'min:1'],
        ]);

        $relationshipTypeCategory = new UpdateRelationshipTypeCategory(
            user: $request->user(),
            relationshipTypeCategory: $relationshipTypeCategory,
            name: $validated['name'],
            position: isset($validated['position'])
                ? (int) $validated['position']
                : $relationshipTypeCategory->position,
        )->execute();

        return new RelationshipTypeCategoryResource($relationshipTypeCategory)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $vault = $request->attributes->get('vault');

        $relationshipTypeCategory = $vault
            ->relationshipTypeCategories()
            ->findOrFail($request->route()->parameter('relationshipTypeCategory'));

        new DestroyRelationshipTypeCategory(
            user: $request->user(),
            relationshipTypeCategory: $relationshipTypeCategory,
        )->execute();

        return response()->noContent(204);
    }
}
