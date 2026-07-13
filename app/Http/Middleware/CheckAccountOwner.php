<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\PermissionEnum;
use App\Models\AccountMember;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountOwner
{
    /**
     * Check if the user is an owner of the account.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $member = $request->attributes->get('member');

        abort_unless($member instanceof AccountMember && $member->role === PermissionEnum::Owner->value, 403);

        return $next($request);
    }
}
