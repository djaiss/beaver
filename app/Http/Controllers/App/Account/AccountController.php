<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Account;

use App\Actions\DestroyAccount;
use App\Actions\UpdateAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        return view('app.settings.account.index', [
            'account' => $request->user()->account,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        new UpdateAccount(
            user: $request->user(),
            account: $request->user()->account,
            name: $validated['name'],
        )->execute();

        return to_route('settings.index')
            ->with('status', __('Account updated successfully'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyAccount(
            user: $request->user(),
            account: $request->user()->account,
        )->execute();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('register')
            ->with('status', __('Account deleted successfully'));
    }
}
