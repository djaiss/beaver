<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\UpdateUserPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\UpdatePasswordRequest;
use Illuminate\Http\RedirectResponse;

class PasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        new UpdateUserPassword(
            user: $request->user(),
            currentPassword: $validated['current_password'],
            newPassword: $validated['new_password'],
        )->execute();

        return to_route('settings.security.index')
            ->with('status', __('app/shared.changes_saved'));
    }
}
