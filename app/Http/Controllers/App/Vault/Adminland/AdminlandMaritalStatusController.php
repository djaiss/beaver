<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\CreateMaritalStatus;
use App\Actions\DestroyMaritalStatus;
use App\Actions\UpdateMaritalStatus;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandMaritalStatusController extends Controller
{
    public function new(Request $request): View
    {
        return view('app.vault.adminland.manage._marital-status-new', [
            'vault' => $request->attributes->get('vault'),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new CreateMaritalStatus(
            user: $request->user(),
            vault: $vault,
            name: $validated['name']
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function edit(Request $request): View
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('maritalStatus');

        try {
            $maritalStatus = $vault->maritalStatuses()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        return view('app.vault.adminland.manage._marital-status-edit', [
            'maritalStatus' => $maritalStatus,
            'vault' => $vault,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('maritalStatus');

        try {
            $maritalStatus = $vault->maritalStatuses()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ]);

        new UpdateMaritalStatus(
            user: $request->user(),
            maritalStatus: $maritalStatus,
            name: $validated['name'],
            position: $maritalStatus->position
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');
        $id = $request->route()->parameter('maritalStatus');

        try {
            $maritalStatus = $vault->maritalStatuses()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        new DestroyMaritalStatus(
            user: $request->user(),
            maritalStatus: $maritalStatus
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }
}
