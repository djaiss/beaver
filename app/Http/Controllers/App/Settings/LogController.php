<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogController extends Controller
{
    public function index(Request $request): View
    {
        $logs = Log::query()
            ->where('user_id', $request->user()->id)
            ->with('user')
            ->latest()
            ->cursorPaginate(10)
            ->through(fn (Log $log) => (object) [
                'username' => $log->getUserName(),
                'action' => $log->action,
                'description' => $log->getTranslatedDescription(),
                'created_at' => $log->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $log->created_at->diffForHumans(),
            ]);

        return view('app.settings.logs.index', [
            'logs' => $logs,
        ]);
    }
}
