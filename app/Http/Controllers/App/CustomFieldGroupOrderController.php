<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\MoveCustomFieldGroup;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldGroupOrderController extends Controller
{
    public function update(Request $request, int $catalogType, int $group): RedirectResponse
    {
        try {
            $type = $request->user()->account->catalogTypes()->findOrFail($catalogType);
            $customFieldGroup = $type->customFieldGroups()->findOrFail($group);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        new MoveCustomFieldGroup(
            user: $request->user(),
            customFieldGroup: $customFieldGroup,
            direction: $validated['direction'],
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Group moved'))
            ->with('status_description', __('The group order was updated.'));
    }
}
