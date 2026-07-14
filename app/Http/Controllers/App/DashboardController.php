<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $account = $user->account;
        $userIds = $account->users()->pluck('id');

        $activity = Log::query()
            ->whereIn('user_id', $userIds)
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (Log $log): object => (object) [
                'name' => $log->getUserName(),
                'description' => $log->getTranslatedDescription(),
                'createdAtHuman' => $log->created_at->diffForHumans(),
            ]);

        return view('app.dashboard.index', [
            'greeting' => $this->greeting(),
            'firstName' => $user->first_name,
            'memberCount' => $userIds->count(),
            'pendingInvitations' => $account->invitations()
                ->whereNull('accepted_at')
                ->where('expires_at', '>', now())
                ->count(),
            'activity' => $activity,
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
