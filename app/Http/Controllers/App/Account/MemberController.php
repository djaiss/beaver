<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Account;

use App\Actions\InviteToAccount;
use App\Actions\RemoveAccountMember;
use App\Actions\UpdateMemberRole;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $account = $request->user()->account;

        $order = [PermissionEnum::Owner->value, PermissionEnum::Editor->value, PermissionEnum::Viewer->value];

        $members = $account->users()->get()
            ->sortBy(fn (User $member): int => array_search($member->role, $order, true))
            ->values();

        $invitations = $account->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();

        return view('app.settings.members.index', [
            'account' => $account,
            'members' => $members,
            'invitations' => $invitations,
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255'],
            'role' => ['required', new Enum(PermissionEnum::class)],
        ]);

        new InviteToAccount(
            user: $request->user(),
            account: $request->user()->account,
            email: $validated['email'],
            role: $validated['role'],
        )->execute();

        return to_route('settings.members.index')
            ->with('status', __('Invitation sent successfully'));
    }

    public function update(Request $request, int $userId): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', new Enum(PermissionEnum::class)],
        ]);

        $account = $request->user()->account;
        $member = $account->users()->findOrFail($userId);

        new UpdateMemberRole(
            user: $request->user(),
            account: $account,
            member: $member,
            role: $validated['role'],
        )->execute();

        return to_route('settings.members.index')
            ->with('status', __('Member role updated successfully'));
    }

    public function destroy(Request $request, int $userId): RedirectResponse
    {
        $account = $request->user()->account;
        $member = $account->users()->findOrFail($userId);

        new RemoveAccountMember(
            user: $request->user(),
            account: $account,
            member: $member,
        )->execute();

        return to_route('settings.members.index')
            ->with('status', __('Member removed successfully'));
    }
}
