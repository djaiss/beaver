<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\CollectionType;

use App\Actions\MoveCustomFieldGroup;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomFieldGroupResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldGroupOrderController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');
        $groupId = $request->route()->parameter('group');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);
        $group = $type->customFieldGroups()->findOrFail($groupId);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        new MoveCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $group,
            direction: $validated['direction'],
        )->execute();

        return new CustomFieldGroupResource($group->refresh())
            ->response()
            ->setStatusCode(200);
    }
}
