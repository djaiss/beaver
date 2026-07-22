<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\DestroyUserAsInstanceAdministrator;
use App\Actions\ToggleInstanceAdministrator;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_instance_administrator' => ['required', 'boolean'],
        ]);

        new ToggleInstanceAdministrator(
            user: $request->user(),
            userToToggle: $user,
            isInstanceAdministrator: (bool) $validated['is_instance_administrator'],
        )->execute();

        return to_route('instanceAdmin.accounts.show', $user->account_id)
            ->with('status', 'User updated successfully')
            ->with('status_description', 'Their access to the instance administration has changed.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $accountId = $user->account_id;

        new DestroyUserAsInstanceAdministrator(
            user: $request->user(),
            userToDelete: $user,
        )->execute();

        return to_route('instanceAdmin.accounts.show', $accountId)
            ->with('status', 'User deleted successfully')
            ->with('status_description', 'The user no longer has access to this instance.');
    }
}
