<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Vault;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
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
        $slug = $request->route()->parameter('slug');
        $id = (int) Str::before($slug, '-');

        try {
            $vault = Vault::query()->findOrFail($id);

            $member = $request->user()->memberOf($vault);
            abort_unless($member !== null, 403);

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
