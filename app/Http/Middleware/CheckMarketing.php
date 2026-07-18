<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMarketing
{
    /**
     * Send visitors straight to the application when the marketing site is off.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('marketing.show')) {
            return $next($request);
        }

        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        return redirect()->route('login');
    }
}
