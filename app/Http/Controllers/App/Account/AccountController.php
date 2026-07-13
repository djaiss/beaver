<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Account;

use App\Actions\AddAccountMember;
use App\Actions\CreateAccount;
use App\Actions\DestroyAccount;
use App\Actions\UpdateAccount;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = $request
            ->user()
            ->accounts()
            ->get()
            ->map(fn (Account $account): object => (object) [
                'name' => $account->name,
                'role' => $account->pivot->role,
                'link' => route('accounts.show', $account->id),
            ]);

        return view('app.account.index', [
            'accounts' => $accounts,
        ]);
    }

    public function new(): View
    {
        return view('app.account.create');
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $account = new CreateAccount(
            author: $request->user(),
            name: $validated['name'],
        )->execute();

        new AddAccountMember(
            account: $account,
            user: $request->user(),
            role: PermissionEnum::Owner->value,
        )->execute();

        return to_route('accounts.show', $account->id)
            ->with('status', __('Account created successfully'));
    }

    public function show(Request $request): View
    {
        return view('app.account.show', [
            'account' => $request->attributes->get('account'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]);

        $account = $request->attributes->get('account');

        new UpdateAccount(
            user: $request->user(),
            account: $account,
            name: $validated['name'],
        )->execute();

        return to_route('accounts.show', $account->id)
            ->with('status', __('Account updated successfully'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        new DestroyAccount(
            user: $request->user(),
            account: $request->attributes->get('account'),
        )->execute();

        return to_route('accounts.index')
            ->with('status', __('Account deleted successfully'));
    }
}
