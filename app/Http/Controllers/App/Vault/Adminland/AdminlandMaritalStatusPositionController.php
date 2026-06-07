<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\UpdateMaritalStatus;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdminlandMaritalStatusPositionController extends Controller
{
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
            'position' => ['required', 'integer', 'min:1'],
        ]);

        new UpdateMaritalStatus(
            user: $request->user(),
            maritalStatus: $maritalStatus,
            name: $maritalStatus->name,
            position: (int) $validated['position']
        )->execute();

        return to_route('vault.adminland.index', $vault->id)
            ->with('status', __('app/shared.changes_saved'));
    }
}
