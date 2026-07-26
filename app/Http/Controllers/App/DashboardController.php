<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Enums\DashboardSectionEnum;
use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Services\AccountDashboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $account = $user->account;

        // Every route out of authentication lands here, so this is the one place that has to
        // know an empty account belongs on the getting started screen instead. Once the account
        // holds a collection there is a dashboard worth showing, and the redirect stops.
        if ($account->show_getting_started && ! $account->catalogs()->exists()) {
            return to_route('gettingStarted.index');
        }

        $dashboard = new AccountDashboard(account: $account);

        $activity = Log::query()
            ->whereIn('user_id', $account->users()->pluck('id'))
            ->with('user')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Log $log): object => (object) [
                'user' => $log->user,
                'name' => $log->getUserName(),
                'description' => $log->getTranslatedDescription(),
                'createdAtHuman' => $log->created_at->diffForHumans(),
            ]);

        return view('app.dashboard.index', [
            'greeting' => $this->greeting(),
            'firstName' => $user->first_name,
            'currency' => $account->currency_code,
            'totals' => $dashboard->totals(),
            'collections' => $dashboard->collections(),
            'recentAdditions' => $dashboard->recentAdditions(),
            'loans' => $dashboard->loanSnapshot(),
            'locations' => $dashboard->valueByLocation(),
            'activity' => $activity,
            'sections' => DashboardSectionEnum::cases(),
            'hiddenSections' => $user->hidden_dashboard_sections ?? [],
        ]);
    }

    private function greeting(): string
    {
        $hour = (int) now()->format('G');

        return match (true) {
            $hour < 12 => __('Good morning'),
            $hour < 18 => __('Good afternoon'),
            default => __('Good evening'),
        };
    }
}
