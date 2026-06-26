<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\UpdateUserInformation;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\UpdateProfileRequest;
use App\ViewModels\Settings\SettingsIndexViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function index(Request $request): View
    {
        $view = new SettingsIndexViewModel(
            user: $request->user(),
        );

        return view('app.settings.index', [
            'view' => $view,
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        new UpdateUserInformation(
            user: $request->user(),
            email: mb_strtolower((string) $validated['email']),
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            nickname: $validated['nickname'],
            locale: $validated['locale'],
            timeFormat24h: $validated['time_format_24h'] === 'true',
        )->execute();

        return to_route('settings.index')
            ->with('status', __('app/shared.changes_saved'));
    }
}
