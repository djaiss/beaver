<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\ToggleGettingStarted;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GettingStartedController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'show_getting_started' => ['required', 'in:yes,no'],
        ]);

        new ToggleGettingStarted(
            user: $request->user(),
            account: $request->user()->account,
            show: $validated['show_getting_started'] === 'yes',
        )->execute();

        return to_route('settings.index')
            ->with('status', __('Account updated successfully'))
            ->with('status_description', __('Your account settings were saved.'));
    }
}
