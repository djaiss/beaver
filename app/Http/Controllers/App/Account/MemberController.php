<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Account;

use App\Actions\InviteToAccount;
use App\Actions\RemoveAccountMember;
use App\Actions\UpdateMemberRole;
use App\Enums\PermissionEnum;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        /** @var Account $account */
        $account = $request->attributes->get('account');

        $members = $account->members()->with('user')->get();

        $invitations = $account->invitations()
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->get();

        return view('app.account.members.index', [
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

        $account = $request->attributes->get('account');

        new InviteToAccount(
            user: $request->user(),
            account: $account,
            email: $validated['email'],
            role: $validated['role'],
        )->execute();

        return to_route('accounts.members.index', $account->id)
            ->with('status', __('Invitation sent successfully'));
    }

    public function update(Request $request, int $accountId, int $memberId): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', new Enum(PermissionEnum::class)],
        ]);

        /** @var Account $account */
        $account = $request->attributes->get('account');

        $member = AccountMember::query()
            ->where('account_id', $account->id)
            ->findOrFail($memberId);

        new UpdateMemberRole(
            user: $request->user(),
            account: $account,
            member: $member,
            role: $validated['role'],
        )->execute();

        return to_route('accounts.members.index', $account->id)
            ->with('status', __('Member role updated successfully'));
    }

    public function destroy(Request $request, int $accountId, int $memberId): RedirectResponse
    {
        /** @var Account $account */
        $account = $request->attributes->get('account');

        $member = AccountMember::query()
            ->where('account_id', $account->id)
            ->findOrFail($memberId);

        new RemoveAccountMember(
            user: $request->user(),
            account: $account,
            member: $member,
        )->execute();

        return to_route('accounts.members.index', $account->id)
            ->with('status', __('Member removed successfully'));
    }
}
