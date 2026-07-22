<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\DocumentationPortal;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Resolves the {locale} URL prefix (en, fr, ...) that every public page sits
 * behind into the internal locale key (en, fr_FR, ...), sets the application
 * locale so the whole page renders in that language, and pins the prefix as the
 * default {locale} route parameter so links back to other public pages stay in
 * the same language without threading the locale through every route() call.
 */
class SetMarketingLocale
{
    public function __construct(
        private DocumentationPortal $portal,
    ) {}

    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $urlLocale = (string) $request->route('locale');
        $locale = $this->portal->localeForUrl($urlLocale);

        // The route only matches available locale prefixes, so this is a guard
        // against a prefix that is configured but no longer has content.
        if ($locale === null || ! $this->portal->hasLocale($locale)) {
            throw new NotFoundHttpException;
        }

        App::setLocale($locale);
        URL::defaults(['locale' => $urlLocale]);

        return $next($request);
    }
}
