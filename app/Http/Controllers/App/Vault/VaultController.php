<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault;

use App\Actions\CreateVault;
use App\Http\Controllers\Controller;
use App\Models\Vault;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VaultController extends Controller
{
    public function index(Request $request): View
    {
        $vaults = $request
            ->user()
            ->vaults()
            ->get()
            ->map(fn (Vault $vault) => (object) [
                'name' => $vault->name,
                'link' => route('vault.show', $vault->id),
                'avatar' => $vault->getAvatar(),
            ]);

        return view('app.vault.index', [
            'vaults' => $vaults,
        ]);
    }

    public function create(): View
    {
        return view('app.vault.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vault_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z0-9\s\-_]+$/',
            ],
        ]);

        $vault = new CreateVault(
            user: $request->user(),
            name: $validated['vault_name'],
        )->execute();

        return to_route('vault.show', $vault->id)
            ->with('status', __('Vault created successfully'));
    }

    public function show(): View
    {
        return view('app.vault.show');
    }
}
