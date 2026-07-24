<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CatalogType;

use App\Actions\CreateCustomFieldGroup;
use App\Actions\DestroyCustomFieldGroup;
use App\Actions\UpdateCustomFieldGroup;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomFieldGroupResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CustomFieldGroupController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $catalogTypeId = $request->route()->parameter('collectionType');
        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $groups = $type->customFieldGroups()
            ->orderBy('position')
            ->orderBy('id')
            ->paginate($perPage);

        return CustomFieldGroupResource::collection($groups);
    }

    public function show(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');
        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);
        $groupId = $request->route()->parameter('group');

        $group = $type->customFieldGroups()->findOrFail($groupId);

        return new CustomFieldGroupResource($group)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');
        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $group = new CreateCustomFieldGroup(
            user: $request->user(),
            catalogType: $type,
            name: $validated['name'] ?? '',
        )->execute();

        return new CustomFieldGroupResource($group)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $catalogTypeId = $request->route()->parameter('collectionType');
        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);
        $groupId = $request->route()->parameter('group');

        $group = $type->customFieldGroups()->findOrFail($groupId);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        $group = new UpdateCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $group,
            name: $validated['name'] ?? '',
        )->execute();

        return new CustomFieldGroupResource($group)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $catalogTypeId = $request->route()->parameter('collectionType');
        $type = $request->user()->account->catalogTypes()->findOrFail($catalogTypeId);
        $groupId = $request->route()->parameter('group');

        $group = $type->customFieldGroups()->findOrFail($groupId);

        new DestroyCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $group,
        )->execute();

        return response()->noContent(204);
    }
}
