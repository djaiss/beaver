<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PermissionEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminland
{
    /**
     * Check if the user has the right to access adminland.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $member = $request->attributes->get('member');

        abort_unless($member->role === PermissionEnum::Owner->value, 403);

        return $next($request);
    }
}
