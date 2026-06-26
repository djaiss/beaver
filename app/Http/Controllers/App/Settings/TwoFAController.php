<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\Generate2faQRCode;
use App\Actions\Remove2fa;
use App\Actions\Validate2faQRCode;
use App\Http\Controllers\Controller;
use App\ViewModels\Settings\TwoFANewViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class TwoFAController extends Controller
{
    public function new(Request $request): View
    {
        $code = new Generate2faQRCode(
            user: $request->user(),
        )->execute();

        $view = new TwoFANewViewModel(
            code: $code,
        );

        return view('app.settings.security._2fa-new', [
            'view' => $view,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'numeric', 'digits:6'],
        ]);

        try {
            new Validate2faQRCode(
                user: $request->user(),
                token: (string) $request->input('token'),
            )->execute();
        } catch (InvalidArgumentException) {
            return back()
                ->withErrors(['token' => __('The provided token is invalid.')])
                ->withInput();
        }

        return to_route('settings.security.index')
            ->with('status', __('Two-factor authentication has been enabled successfully.'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new Remove2fa(
            user: $request->user(),
        )->execute();

        return to_route('settings.security.index')
            ->with('status', __('app/shared.changes_saved'));
    }
}
