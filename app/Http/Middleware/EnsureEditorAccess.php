<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEditorAccess
{
    /**
     * Only owners and editors may manage collection types.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->account->allowsManagementBy($request->user()) === true, 404);

        return $next($request);
    }
}
