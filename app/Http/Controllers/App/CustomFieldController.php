<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCustomField;
use App\Actions\DestroyCustomField;
use App\Actions\UpdateCustomField;
use App\Enums\FieldTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\CollectionType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldController extends Controller
{
    public function create(Request $request, int $collectionType): RedirectResponse
    {
        $type = $this->findType($request, $collectionType);

        new CreateCustomField(
            user: $request->user(),
            collectionType: $type,
            name: '',
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Field added'));
    }

    public function update(Request $request, int $collectionType, int $customField): RedirectResponse
    {
        $type = $this->findType($request, $collectionType);

        try {
            $field = $type->customFields()->findOrFail($customField);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'field_type' => ['required', Rule::enum(FieldTypeEnum::class)],
            'options' => ['array'],
            'options.*' => ['nullable', 'string', 'max:255'],
        ]);

        $isSelect = $validated['field_type'] === FieldTypeEnum::Select->value;

        // The trailing "add option" input arrives empty (and becomes null via
        // ConvertEmptyStringsToNull); keep only the real, non-blank options.
        $options = array_values(array_filter(
            $validated['options'] ?? [],
            fn ($option): bool => is_string($option) && trim($option) !== '',
        ));

        new UpdateCustomField(
            user: $request->user(),
            customField: $field,
            name: $validated['name'] ?? '',
            fieldType: $validated['field_type'],
            options: $isSelect ? $options : null,
            position: $field->position,
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Field updated'));
    }

    public function destroy(Request $request, int $collectionType, int $customField): RedirectResponse
    {
        $type = $this->findType($request, $collectionType);

        try {
            $field = $type->customFields()->findOrFail($customField);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyCustomField(
            user: $request->user(),
            customField: $field,
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Field removed'));
    }

    private function findType(Request $request, int $collectionType): CollectionType
    {
        try {
            return $request->user()->account->collectionTypes()->findOrFail($collectionType);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
