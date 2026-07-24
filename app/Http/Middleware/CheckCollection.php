<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the collection every url under `collections/{collection}` carries.
 *
 * A collection outside the caller's account is not found rather than forbidden,
 * so one account cannot learn what another holds. The model is put back on the
 * route so a controller may type hint it, and shared with the views so the
 * screens around it do not have to be handed it every time.
 */
class CheckCollection
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = (int) $request->route()->parameter('collection');

        try {
            $collection = $request->user()->account->collections()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $request->route()->setParameter('collection', $collection);
        $request->attributes->set('collection', $collection);

        View::share('collection', $collection);

        return $next($request);
    }
}
