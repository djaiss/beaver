<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Account;

use App\Actions\AcceptInvitation;
use App\Actions\CreateUser;
use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function show(string $token): View
    {
        $invitation = Invitation::query()->where('token', $token)->firstOrFail();

        return view('app.account.invitations.show', [
            'invitation' => $invitation,
            'hasAccount' => User::query()->where('email', $invitation->email)->exists(),
        ]);
    }

    public function create(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::query()->where('token', $token)->firstOrFail();

        // A user belongs to exactly one account, so anyone who already has one
        // cannot claim an invitation into another account.
        if (Auth::check() || User::query()->where('email', $invitation->email)->exists()) {
            return to_route('login')
                ->with('status', __('An account already exists for this email address.'));
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'password' => [
                'required',
                'string',
                'max:255',
                'confirmed',
                Password::min(8)->uncompromised(),
            ],
        ]);

        $user = new CreateUser(
            account: $invitation->account,
            email: $invitation->email,
            password: $validated['password'],
            firstName: $validated['first_name'],
            lastName: $validated['last_name'],
            role: $invitation->role,
        )->execute();

        $user->email_verified_at = now();
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        new AcceptInvitation(
            invitation: $invitation,
            user: $user,
        )->execute();

        return to_route('dashboard.index')
            ->with('status', __('Welcome! You have joined the account.'))
            ->with('status_description', __("You can now access the account's collections."));
    }
}
