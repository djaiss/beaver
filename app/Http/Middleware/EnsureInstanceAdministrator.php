<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureInstanceAdministrator
{
    /**
     * Only instance administrators may reach the instance administration. This
     * answers 404 rather than 403 so the panel does not announce itself to the
     * users who may not open it.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->isInstanceAdministrator() === true, 404);

        return $next($request);
    }
}
