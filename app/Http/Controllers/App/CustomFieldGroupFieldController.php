<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateCustomField;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CustomFieldGroupFieldController extends Controller
{
    public function create(Request $request, int $collectionType, int $group): RedirectResponse
    {
        try {
            $type = $request->user()->account->collectionTypes()->findOrFail($collectionType);
            $customFieldGroup = $type->customFieldGroups()->findOrFail($group);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new CreateCustomField(
            user: $request->user(),
            collectionType: $type,
            name: '',
            group: $customFieldGroup,
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Field added'))
            ->with('status_description', __('A new custom field was added to the group.'));
    }
}
