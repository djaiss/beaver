<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\UpdateGender;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminlandGenderPositionController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('gender');

        try {
            $gender = $vault->genders()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'position' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        new UpdateGender(
            user: $request->user(),
            gender: $gender,
            name: $gender->name,
            position: (int) $validated['position'],
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('Changes saved'));
    }
}
