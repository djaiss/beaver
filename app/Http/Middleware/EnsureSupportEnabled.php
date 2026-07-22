<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSupportEnabled
{
    /**
     * The support section only exists when the instance turns it on. When it is
     * off, every support route answers 404 rather than 403 so the section stays
     * invisible, matching how the sidebar hides its link.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(config('support.enabled') === true, 404);

        return $next($request);
    }
}
