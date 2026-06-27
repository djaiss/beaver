<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault;

use App\Actions\JoinVault;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JoinVaultController extends Controller
{
    public function new(): View
    {
        return view('app.vault.join.create');
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invitation_code' => [
                'required',
                'string',
                'max:64',
            ],
        ]);

        $vault = new JoinVault(
            user: $request->user(),
            invitationCode: $validated['invitation_code'],
        )->execute();

        return to_route('vault.show', $vault->id)
            ->with('status', __('Welcome '));
    }
}
