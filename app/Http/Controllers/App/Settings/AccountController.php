<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Settings\DestroyAccountRequest;
use App\ViewModels\Settings\AccountIndexViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $view = new AccountIndexViewModel(
            user: $request->user(),
        );

        return view('app.settings.account.index', [
            'view' => $view,
        ]);
    }

    public function destroy(DestroyAccountRequest $request): RedirectResponse
    {
        new DestroyAccount(
            user: $request->user(),
            reason: $request->input('feedback'),
        )->execute();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return to_route('login');
    }
}
