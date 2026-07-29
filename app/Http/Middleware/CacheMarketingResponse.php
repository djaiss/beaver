<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tell the CDN in front of us, and the visitor's browser, how long a public page
 * may be reused. The pages change only when the site is redeployed or when an
 * instance administrator edits something the public site shows, so the shared
 * cache holds them for a week and is emptied on demand through CloudflareCache.
 *
 * The browser lifetime is deliberately short. A purge reaches the CDN and
 * nothing else, so a page a visitor already holds cannot be taken back: five
 * minutes is the longest a mistake stays on screen. That lifetime matters twice
 * over for the bare domain: a browser handed a 301 with no lifetime on it may
 * keep the redirect for good and never ask again.
 */
class CacheMarketingResponse
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('marketing.cache_public_pages')) {
            return $response;
        }

        if ($request->method() !== 'GET') {
            return $response;
        }

        // A rendered page, or the permanent redirect the bare domain answers
        // with. Everything else must never be held anywhere: a 404 from an
        // unknown slug, and above all the 302 to the login page an instance with
        // the public site switched off answers with. That is why this names 301
        // rather than allowing any redirect through.
        if (! in_array($response->getStatusCode(), [200, 301], true)) {
            return $response;
        }

        $response->headers->set('Cache-Control', implode(', ', [
            'public',
            'max-age='.config('marketing.browser_cache_seconds'),
            's-maxage='.config('marketing.cdn_cache_seconds'),
            'stale-while-revalidate=60',
        ]));

        return $response;
    }
}
