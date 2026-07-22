<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Instance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Collection;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class OverviewController extends Controller
{
    public function index(): View
    {
        return view('app.instance.index', [
            'accountCount' => Account::query()->count(),
            'userCount' => User::query()->count(),
            'collectionCount' => Collection::query()->count(),
            'itemCount' => Item::query()->count(),
            'accountsThisMonth' => Account::query()->where('created_at', '>=', Carbon::now()->startOfMonth())->count(),
            'activeThisMonth' => User::query()->where('last_activity_at', '>=', Carbon::now()->startOfMonth())->count(),
            'signups' => $this->signupsPerMonth(),
        ]);
    }

    /**
     * How many accounts were created in each of the last twelve months, oldest
     * first. Counted in PHP off the created_at timestamps, which keeps the
     * grouping identical on sqlite and mysql.
     *
     * @return list<array{label: string, count: int}>
     */
    private function signupsPerMonth(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);

        $counts = Account::query()
            ->where('created_at', '>=', $start)
            ->pluck('created_at')
            ->countBy(fn (Carbon $date): string => $date->format('Y-m'));

        $months = [];

        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);

            $months[] = [
                'label' => $month->format('M'),
                'count' => (int) $counts->get($month->format('Y-m'), 0),
            ];
        }

        return $months;
    }
}
