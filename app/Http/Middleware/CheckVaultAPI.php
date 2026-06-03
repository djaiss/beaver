<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Vault;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVaultAPI
{
    /**
     * Check if the user has access to the vault.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->route()->parameter('id');

        try {
            $vault = Vault::query()->findOrFail($id);

            abort_unless($request->user()->memberOf($vault) !== null, 403);

            $request->attributes->add(['vault' => $vault]);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
