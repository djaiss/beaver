<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Throwable;

/**
 * The languages offered by the footer picker, each with the URL that lands the
 * visitor on the page they are already reading, in that language.
 *
 * The public site carries its language in the URL (getkollek.com/fr/pricing), so
 * switching language is mostly a matter of rebuilding the current route with
 * another {locale} prefix. The one place that does not work is the documentation
 * portal: its section and slug are translated per locale, so the same page has a
 * different URL in every language and has to be resolved through its page id.
 */
class MarketingLanguages
{
    private const DOCUMENTATION_ROUTE = 'marketing.docs.portal.show';

    public function __construct(
        private DocumentationPortal $portal,
    ) {}

    /**
     * Every locale that has content on disk, in the order they are configured.
     *
     * "translated" says whether that language really carries the page being read,
     * as opposed to the link falling back to the portal home. The picker shows
     * every language either way; the SEO tags only claim the ones that are real.
     *
     * @return array<int, array{locale: string, code: string, label: string, flag: string, url: string, current: bool, translated: bool}>
     */
    public function links(Request $request): array
    {
        $current = app()->getLocale();

        // Resolved once rather than per language: the page being read is the same
        // whichever language the visitor is about to pick.
        $documentationId = $this->documentationId($request);

        $links = [];

        foreach (config('docs.locales') as $locale => $meta) {
            if (! $this->portal->hasLocale($locale)) {
                continue;
            }

            $links[] = [
                'locale' => $locale,
                'code' => $meta['code'],
                'label' => $meta['label'],
                'flag' => $meta['flag'],
                'url' => $this->urlFor($request, $locale, $meta['url'], $documentationId),
                'current' => $locale === $current,
                'translated' => $this->isTranslatedInto($request, $locale, $documentationId),
            ];
        }

        return $links;
    }

    /**
     * Whether the picker has anything to offer. An instance whose docs folder
     * only carries one language does not need a language menu.
     */
    public function isOffered(): bool
    {
        return count($this->portal->availableLocales()) > 1;
    }

    private function urlFor(Request $request, string $locale, string $urlLocale, ?string $documentationId): string
    {
        $route = $request->route();

        if ($route === null || $route->getName() === null) {
            return route('marketing.index', ['locale' => $urlLocale]);
        }

        if ($route->getName() === self::DOCUMENTATION_ROUTE) {
            return $this->documentationUrl($locale, $urlLocale, $documentationId);
        }

        // Every other public route keeps its parameters and swaps the prefix. A
        // route the target locale cannot build would throw, and the home page in
        // that language is a better answer than an error.
        try {
            return route($route->getName(), array_merge($route->parameters(), ['locale' => $urlLocale]));
        } catch (Throwable) {
            return route('marketing.index', ['locale' => $urlLocale]);
        }
    }

    /**
     * Whether the page being read exists in that language. Only the documentation
     * portal can answer no: everywhere else the page is the same route with
     * another prefix, and the interface around it is translated in full.
     */
    private function isTranslatedInto(Request $request, string $locale, ?string $documentationId): bool
    {
        if ($request->route()?->getName() !== self::DOCUMENTATION_ROUTE) {
            return true;
        }

        if ($documentationId === null) {
            return false;
        }

        return collect($this->portal->pagesFor($locale))->contains('id', $documentationId);
    }

    /**
     * The id of the documentation page being read, or null when the current page
     * is not one.
     */
    private function documentationId(Request $request): ?string
    {
        if ($request->route()?->getName() !== self::DOCUMENTATION_ROUTE) {
            return null;
        }

        $resolved = $this->portal->find(
            app()->getLocale(),
            (string) $request->route('section'),
            (string) $request->route('slug'),
        );

        return $resolved === null ? null : (string) $resolved['page']['id'];
    }

    /**
     * The same documentation page in another language. A page that has not been
     * translated yet has no URL in that language at all, so the picker points at
     * the portal home rather than at the English page, which would leave the
     * visitor in the language they just left.
     */
    private function documentationUrl(string $locale, string $urlLocale, ?string $id): string
    {
        $home = route('marketing.docs.portal.home.show', ['locale' => $urlLocale]);

        if ($id === null) {
            return $home;
        }

        $page = collect($this->portal->pagesFor($locale))->firstWhere('id', $id);

        if ($page === null || $page['is_home']) {
            return $home;
        }

        return $this->portal->urlFor($locale, $page);
    }
}
