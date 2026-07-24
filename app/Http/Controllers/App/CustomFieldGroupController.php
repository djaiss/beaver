<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCustomFieldGroup;
use App\Actions\DestroyCustomFieldGroup;
use App\Actions\UpdateCustomFieldGroup;
use App\Http\Controllers\Controller;
use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomFieldGroupController extends Controller
{
    public function create(Request $request, int $catalogType): RedirectResponse
    {
        $type = $this->findType($request, $catalogType);

        new CreateCustomFieldGroup(
            user: $request->user(),
            catalogType: $type,
            name: '',
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Group added'))
            ->with('status_description', __('A new field group was added to the type.'));
    }

    public function update(Request $request, int $catalogType, int $group): RedirectResponse
    {
        $type = $this->findType($request, $catalogType);
        $customFieldGroup = $this->findGroup($type, $group);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
        ]);

        new UpdateCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $customFieldGroup,
            name: $validated['name'] ?? '',
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Group updated'))
            ->with('status_description', __('Your changes to the group were saved.'));
    }

    public function destroy(Request $request, int $catalogType, int $group): RedirectResponse
    {
        $type = $this->findType($request, $catalogType);
        $customFieldGroup = $this->findGroup($type, $group);

        new DestroyCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $customFieldGroup,
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Group removed'))
            ->with('status_description', __('The group was removed. Its fields are now standalone.'));
    }

    private function findType(Request $request, int $catalogType): CatalogType
    {
        try {
            return $request->user()->account->catalogTypes()->findOrFail($catalogType);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findGroup(CatalogType $type, int $group): CustomFieldGroup
    {
        try {
            return $type->customFieldGroups()->findOrFail($group);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
