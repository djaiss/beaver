<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\CreateGender;
use App\Actions\DestroyGender;
use App\Actions\UpdateGender;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandGenderController extends Controller
{
    public function new(Request $request): View
    {
        return view('app.vault.adminland.manage._gender-new', [
            'vault' => $request->attributes->get('vault'),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new CreateGender(
            user: $request->user(),
            vault: $vault,
            name: $validated['name'],
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function edit(Request $request): View
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('gender');

        try {
            $gender = $vault->genders()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('app.vault.adminland.manage._gender-edit', [
            'gender' => $gender,
            'vault' => $vault,
        ]);
    }

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
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new UpdateGender(
            user: $request->user(),
            gender: $gender,
            name: $validated['name'],
            position: $gender->position,
        )->execute();

        return to_route('vault.adminland.index', $request->attributes->get('vault')->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('gender');

        try {
            $gender = $vault->genders()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyGender(
            user: $request->user(),
            gender: $gender,
        )->execute();

        return to_route('vault.adminland.index', $request->attributes->get('vault')->id)
            ->with('status', __('app/shared.changes_saved'));
    }
}
