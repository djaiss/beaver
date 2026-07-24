<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account\CollectionType;

use App\Actions\MoveCustomField;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomFieldResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldOrderController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $collectionTypeId = $request->route()->parameter('collectionType');
        $customFieldId = $request->route()->parameter('customField');

        $type = $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);
        $field = $type->customFields()->findOrFail($customFieldId);

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        new MoveCustomField(
            user: $request->user(),
            customField: $field,
            direction: $validated['direction'],
        )->execute();

        return new CustomFieldResource($field->refresh())
            ->response()
            ->setStatusCode(200);
    }
}
