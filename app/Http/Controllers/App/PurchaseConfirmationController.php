<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\RecordPurchaseConsent;
use App\Enums\PurchaseConsentChoice;
use App\Http\Controllers\Controller;
use App\Services\AccountPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseConfirmationController extends Controller
{
    public function new(Request $request): View
    {
        $user = $request->user();

        return view('app.upgrade.confirm', [
            'used' => new AccountPlan(account: $user->account)->itemsUsed(),
            'choices' => PurchaseConsentChoice::cases(),
        ]);
    }

    public function create(Request $request): RedirectResponse
    {
        // Every point has to be ticked, and it is checked here rather than only in
        // the browser: what is recorded afterwards is meant to stand up on its own.
        $request->validate(
            array_reduce(
                PurchaseConsentChoice::cases(),
                function (array $rules, PurchaseConsentChoice $choice): array {
                    $rules[$choice->value] = ['accepted'];

                    return $rules;
                },
                [],
            ),
        );

        new RecordPurchaseConsent(
            user: $request->user(),
            account: $request->user()->account,
            ipAddress: $request->ip(),
        )->execute();

        // The payment processor is not wired up yet, so this is where the flow
        // stops. What was confirmed is recorded either way.
        return to_route('upgrade.confirm.new')
            ->with('status', __('Your confirmations were recorded'))
            ->with('status_description', __('Checkout is not open yet. We will email you the moment it is, and nothing has been charged.'));
    }
}
