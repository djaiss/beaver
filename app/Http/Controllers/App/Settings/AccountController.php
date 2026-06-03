<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\DestroyAccount;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(): View
    {
        $vaultsToDelete = auth()->user()->memberships()
            ->where('role', 'owner')
            ->with('vault')
            ->get()
            ->filter(fn ($membership): bool => $membership->vault->members()
                ->where('role', 'owner')
                ->count() === 1)
            ->map(fn ($membership) => (object) [
                'name' => $membership->vault->name,
                'link' => route('vault.show', $membership->vault->id),
                'avatar' => $membership->vault->getAvatar(),
            ]);

        // vaults where there are other owners, so the vault won't be deleted
        // but we want to warn the user about it
        $vaultsNotDeleted = auth()->user()->memberships()
            ->where('role', 'owner')
            ->with('vault')
            ->get()
            ->filter(fn ($membership): bool => $membership->vault->members()
                ->where('role', 'owner')
                ->count() > 1)
            ->map(fn ($membership) => (object) [
                'name' => $membership->vault->name,
                'link' => route('vault.show', $membership->vault->id),
                'avatar' => $membership->vault->getAvatar(),
            ]);

        return view('app.settings.account.index', [
            'vaultsToDelete' => $vaultsToDelete,
            'vaultsNotDeleted' => $vaultsNotDeleted,
        ]);
    }

    public function destroy(Request $request): RedirectResponse
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
