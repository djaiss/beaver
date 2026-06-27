<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Vault\Adminland;

use App\Actions\DestroyVault;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminlandManageController extends Controller
{
    public function index(): View
    {
        return view('app.vault.adminland.manage.index');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $vault = $request->attributes->get('vault');

        new DestroyVault(
            user: $request->user(),
            vault: $vault,
        )->execute();

        return to_route('vault.index')
            ->with('status', __('Changes saved'));
    }
}
