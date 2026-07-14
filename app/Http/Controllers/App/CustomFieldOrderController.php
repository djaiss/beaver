<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\MoveCustomField;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomFieldOrderController extends Controller
{
    public function update(Request $request, int $collectionType, int $customField): RedirectResponse
    {
        try {
            $type = $request->user()->account->collectionTypes()->findOrFail($collectionType);
            $field = $type->customFields()->findOrFail($customField);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'direction' => ['required', Rule::in(['up', 'down'])],
        ]);

        new MoveCustomField(
            user: $request->user(),
            customField: $field,
            direction: $validated['direction'],
        )->execute();

        return to_route('settings.types.edit', $type->id)
            ->with('status', __('Field moved'));
    }
}
