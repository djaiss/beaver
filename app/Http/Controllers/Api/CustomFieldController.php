<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateCustomField;
use App\Actions\DestroyCustomField;
use App\Actions\UpdateCustomField;
use App\Enums\FieldTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomFieldResource;
use App\Models\CollectionType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class CustomFieldController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $type = $this->findType($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $fields = $type->customFields()
            ->orderBy('position')
            ->orderBy('id')
            ->paginate($perPage);

        return CustomFieldResource::collection($fields);
    }

    public function show(Request $request): JsonResponse
    {
        $type = $this->findType($request);
        $customFieldId = $request->route()->parameter('customField');

        $field = $type->customFields()->findOrFail($customFieldId);

        return new CustomFieldResource($field)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $type = $this->findType($request);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'field_type' => ['required', Rule::enum(FieldTypeEnum::class)],
            'options' => ['array'],
            'options.*' => ['nullable', 'string', 'max:255'],
        ]);

        $field = new CreateCustomField(
            user: $request->user(),
            collectionType: $type,
            name: $validated['name'] ?? '',
            fieldType: $validated['field_type'],
            options: $this->options($validated),
        )->execute();

        return new CustomFieldResource($field)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $type = $this->findType($request);
        $customFieldId = $request->route()->parameter('customField');

        $field = $type->customFields()->findOrFail($customFieldId);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'field_type' => ['required', Rule::enum(FieldTypeEnum::class)],
            'options' => ['array'],
            'options.*' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:1'],
        ]);

        $field = new UpdateCustomField(
            user: $request->user(),
            customField: $field,
            name: $validated['name'] ?? '',
            fieldType: $validated['field_type'],
            options: $this->options($validated),
            position: $validated['position'] ?? $field->position,
        )->execute();

        return new CustomFieldResource($field)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $type = $this->findType($request);
        $customFieldId = $request->route()->parameter('customField');

        $field = $type->customFields()->findOrFail($customFieldId);

        new DestroyCustomField(
            user: $request->user(),
            customField: $field,
        )->execute();

        return response()->noContent(204);
    }

    private function findType(Request $request): CollectionType
    {
        $collectionTypeId = $request->route()->parameter('collectionType');

        return $request->user()->account->collectionTypes()->findOrFail($collectionTypeId);
    }

    /**
     * Options only apply to select fields; keep the real, non-blank ones.
     *
     * @param  array<string, mixed>  $validated
     * @return array<int, string>|null
     */
    private function options(array $validated): ?array
    {
        if ($validated['field_type'] !== FieldTypeEnum::Select->value) {
            return null;
        }

        return array_values(array_filter(
            $validated['options'] ?? [],
            fn ($option): bool => is_string($option) && trim($option) !== '',
        ));
    }
}
