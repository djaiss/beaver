<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Item;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the copy every url under `copies/{copy}` carries. Runs after
 * CheckItem, and looks the copy up through the item in the url, so a copy of
 * another item is not found.
 *
 * Nothing is shared with the views here, unlike the collection and the item: a
 * copy is only ever written to, and every one of these routes answers with a
 * redirect.
 */
class CheckCopy
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $item = $request->route()->parameter('item');
        abort_unless($item instanceof Item, 404);

        $id = (int) $request->route()->parameter('copy');

        try {
            $copy = $item->copies()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $request->route()->setParameter('copy', $copy);
        $request->attributes->set('copy', $copy);

        return $next($request);
    }
}
