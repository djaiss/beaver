<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMarketing
{
    /**
     * Send visitors to the application when the marketing site is off.
     *
     * Everyone lands on the login page, signed in or not. The guest middleware
     * guarding that page then carries anyone already signed in to their dashboard.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('marketing.show')) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
