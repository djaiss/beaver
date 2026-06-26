<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\ToggleAutoDeleteAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\ToggleAutoDeleteAccountRequest;
use Illuminate\Http\RedirectResponse;

class AutoDeleteAccountController extends Controller
{
    public function update(ToggleAutoDeleteAccountRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        new ToggleAutoDeleteAccount(
            user: $request->user(),
            autoDeleteAccount: $validated['auto_delete_account'] === 'yes',
        )->execute();

        return to_route('settings.security.index')
            ->with('status', trans('app/shared.changes_saved'));
    }
}
