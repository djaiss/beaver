<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\ToggleAutoDeleteUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AutoDeleteUserController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'auto_delete_user' => ['required', 'in:yes,no'],
        ]);

        new ToggleAutoDeleteUser(
            user: $request->user(),
            autoDeleteUser: $request->input('auto_delete_user') === 'yes',
        )->execute();

        return to_route('profile.security.index')
            ->with('status', trans('Changes saved'));
    }
}
