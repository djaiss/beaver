<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class CheckVault
{
    /**
     * Check if the user is a member of the vault.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->route()->parameter('vaultId');

        try {
            $member = Member::query()
                ->with('vault')
                ->where('vault_id', $id)
                ->where('user_id', $request->user()->id)
                ->firstOrFail();

            $vault = $member->vault;

            $request->attributes->add(['vault' => $vault]);
            $request->attributes->add(['member' => $member]);

            View::share('vault', $vault);
            View::share('member', $member);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
