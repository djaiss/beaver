<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Catalog;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the item every url under `items/{item}` carries. Runs after
 * CheckCatalog, and looks the item up through the collection in the url, so
 * an item of another collection is not found even within the same account.
 *
 * Nothing is eager loaded here: each screen loads the relations its own panel
 * reads, which is a page concern rather than a lookup one.
 */
class CheckItem
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $catalog = $request->route()->parameter('collection');
        abort_unless($catalog instanceof Catalog, 404);

        $id = (int) $request->route()->parameter('item');

        try {
            $item = $catalog->items()->findOrFail($id);
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $request->route()->setParameter('item', $item);
        $request->attributes->set('item', $item);

        View::share('item', $item);

        return $next($request);
    }
}
