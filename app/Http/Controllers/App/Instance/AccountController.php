<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Actions\DestroyAccountAsInstanceAdministrator;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Log;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'in:owner,editor,viewer'],
        ]);

        $search = $validated['search'] ?? null;
        $role = $validated['role'] ?? null;

        // Account names and people's names are encrypted at rest, so they cannot
        // be matched in SQL. Email is stored in the clear and is what the search
        // box actually looks at.
        //
        // Both conditions go in one whereHas so they describe the same person:
        // splitting them would match an account that merely has some owner and,
        // separately, somebody whose email matches.
        $accounts = Account::query()
            ->withCount(['users', 'catalogs'])
            ->when($search !== null || $role !== null, fn (Builder $query): Builder => $query->whereHas(
                'users',
                function (Builder $users) use ($search, $role): Builder {
                    if ($search !== null) {
                        $users->where('email', 'like', '%'.addcslashes($search, '%_\\').'%');
                    }

                    if ($role !== null) {
                        $users->where('role', $role);
                    }

                    return $users;
                },
            ))
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('app.instance.accounts.index', [
            'accounts' => $accounts,
            'totalCount' => Account::query()->count(),
            'search' => $search,
            'role' => $role,
            'roles' => PermissionEnum::cases(),
        ]);
    }

    public function show(Account $account): View
    {
        $order = [PermissionEnum::Owner->value, PermissionEnum::Editor->value, PermissionEnum::Viewer->value];

        // An unknown role sorts last rather than blowing up on the int return type.
        $members = $account->users()->get()
            ->sortBy(fn (User $member): int => array_search($member->role, $order, true) === false
                ? count($order)
                : (int) array_search($member->role, $order, true))
            ->values();

        $activity = Log::query()
            ->with('user')
            ->whereIn('user_id', $members->pluck('id'))
            ->orderByDesc('created_at')
            ->limit(15)
            ->get();

        return view('app.instance.accounts.show', [
            'account' => $account,
            'members' => $members,
            'activity' => $activity,
            'catalogCount' => $account->catalogs()->count(),
            'itemCount' => $account->items()->count(),
        ]);
    }

    public function destroy(Request $request, Account $account): RedirectResponse
    {
        new DestroyAccountAsInstanceAdministrator(
            user: $request->user(),
            account: $account,
        )->execute();

        return to_route('instanceAdmin.accounts.index')
            ->with('status', 'Account deleted successfully')
            ->with('status_description', 'The account and everything it contained are gone.');
    }
}
