<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault;

use App\Actions\CreateVault;
use App\Http\Controllers\Controller;
use App\Http\Requests\App\Vault\VaultStoreRequest;
use App\ViewModels\Vault\VaultIndexViewModel;
use App\ViewModels\Vault\VaultNewViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VaultController extends Controller
{
    public function index(Request $request): View
    {
        $view = new VaultIndexViewModel(
            user: $request->user(),
        );

        return view('app.vault.index', [
            'view' => $view,
        ]);
    }

    public function new(): View
    {
        $view = new VaultNewViewModel;

        return view('app.vault.new', [
            'view' => $view,
        ]);
    }

    public function create(VaultStoreRequest $request): RedirectResponse
    {
        $validated = $request->validated();

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
