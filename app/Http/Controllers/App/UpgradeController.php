<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Services\AccountPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UpgradeController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $plan = new AccountPlan(account: $user->account);
        $used = $plan->itemsUsed();
        $free = $plan->freeLimit();

        return view('app.upgrade.index', [
            'used' => $used,
            'freeLimit' => $free,
            'hardLimit' => $plan->hardLimit(),
            'over' => $plan->itemsOverLimit(),
            'remaining' => $plan->itemsRemaining(),
            'isOverFreeLimit' => $plan->isOverFreeLimit(),
            'hasReachedHardLimit' => $plan->hasReachedHardLimit(),
            // The bar splits into what the free plan covers and what sits above
            // it, so both widths are worked out against the larger of the two.
            'freeWidth' => round(min($used, $free) / max($used, $free) * 100, 1),
            'overWidth' => round(max(0, $used - $free) / max($used, $free) * 100, 1),
            // Only an owner can commit the account to a payment.
            'canPurchase' => $user->account->isOwnedBy($user),
        ]);
    }
}
