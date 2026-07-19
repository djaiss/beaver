<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateCategory;
use App\Actions\DestroyCategory;
use App\Actions\UpdateCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $collection = $this->findCollection($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $categories = $collection->categories()
            ->orderBy('id')
            ->paginate($perPage);

        return CategoryResource::collection($categories);
    }

    public function show(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);
        $categoryId = $request->route()->parameter('category');

        $category = $collection->categories()->findOrFail($categoryId);

        return new CategoryResource($category)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $category = new CreateCategory(
            user: $request->user(),
            collection: $collection,
            name: $validated['name'],
            parentId: $validated['parent_id'] ?? null,
            description: $validated['description'] ?? null,
        )->execute();

        return new CategoryResource($category)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $collection = $this->findCollection($request);
        $categoryId = $request->route()->parameter('category');

        $category = $collection->categories()->findOrFail($categoryId);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $category = new UpdateCategory(
            user: $request->user(),
            category: $category,
            name: $validated['name'],
            parentId: $validated['parent_id'] ?? null,
            description: $validated['description'] ?? null,
        )->execute();

        return new CategoryResource($category)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $collection = $this->findCollection($request);
        $categoryId = $request->route()->parameter('category');

        $category = $collection->categories()->findOrFail($categoryId);

        new DestroyCategory(
            user: $request->user(),
            category: $category,
        )->execute();

        return response()->noContent(204);
    }

    private function findCollection(Request $request): Collection
    {
        $collectionId = $request->route()->parameter('collection');

        return $request->user()->account->collections()->findOrFail($collectionId);
    }
}
