<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\ToggleGettingStarted;
use App\Http\Controllers\Controller;
use App\Services\GettingStarted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GettingStartedController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $account = $user->account;

        $checklist = new GettingStarted($account);

        return view('app.gettingStarted.index', [
            'firstName' => $user->first_name,
            'steps' => $checklist->steps(),
            'doneCount' => $checklist->doneCount(),
            // Dismissing hides the screen for everyone in the account, so only an owner is
            // offered the choice. Everyone else just gets the checklist.
            'canDismiss' => $account->isOwnedBy($user),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        new ToggleGettingStarted(
            user: $request->user(),
            account: $request->user()->account,
            show: false,
        )->execute();

        return to_route('dashboard.index')
            ->with('status', __('Getting started dismissed'))
            ->with('status_description', __('You can bring it back from your account settings.'));
    }
}
