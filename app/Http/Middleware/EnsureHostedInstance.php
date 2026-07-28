<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureHostedInstance
{
    /**
     * There is nothing to buy on a self hosted instance, so the upgrade screens
     * only exist on the managed service. Everywhere else they answer 404 rather
     * than 403, the same way the support section does when it is switched off.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('pricing.hosted') === true, 404);

        return $next($request);
    }
}
