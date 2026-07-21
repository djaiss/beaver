<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs\Concerns;

use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Shared by the documentation portal controllers, which all resolve a page
 * and render it inside the same three panel shell.
 */
trait RendersDocumentationPage
{
    /**
     * @param  array{page: array<string, mixed>, fallback: bool}  $resolved
     */
    private function renderPage(string $locale, array $resolved): View
    {
        // Render the whole page (content and chrome) in the documentation
        // locale, so a French page reads French end to end.
        app()->setLocale($locale);

        $page = $resolved['page'];
        $parts = $this->parser->split(file_get_contents($page['path']));
        $rendered = $this->parser->render($parts['body'], $locale);

        return view('marketing.docs.portal.show', [
            'locale' => $locale,
            'urlLocale' => $this->portal->urlLocaleFor($locale),
            'availableLocales' => $this->portal->availableLocales(),
            'navigation' => $this->portal->navigation($locale),
            'page' => $page,
            'content' => $rendered['html'],
            'toc' => $rendered['toc'],
            'fallback' => $resolved['fallback'],
            'languageUrls' => $this->languageUrls($locale, $page),
        ]);
    }

    /**
     * The URL to reach the same page in every offered locale, plus whether that
     * locale actually carries a translation (so the picker can flag the gap).
     *
     * @return array<int, array{locale: string, code: string, label: string, flag: string, url: string, translated: bool, current: bool}>
     */
    private function languageUrls(string $current, array $page): array
    {
        $links = [];

        foreach (config('docs.locales') as $locale => $meta) {
            if (! $this->portal->hasLocale($locale)) {
                continue;
            }

            // Resolve by id, never by reusing the current page's section and
            // slug: those are localized per locale, so the French URL is not
            // a drop-in replacement for the English one.
            $links[] = [
                'locale' => $locale,
                'code' => $meta['code'],
                'label' => $meta['label'],
                'flag' => $meta['flag'],
                'url' => $this->portal->urlForId($page['id'], $locale) ?? route('marketing.docs.portal.index'),
                'translated' => $this->pageExistsIn($locale, $page),
                'current' => $locale === $current,
            ];
        }

        return $links;
    }

    private function pageExistsIn(string $locale, array $page): bool
    {
        return collect($this->portal->pagesFor($locale))->contains('id', $page['id']);
    }

    /**
     * Translate the URL language prefix (fr) into the internal locale key
     * (fr_FR), 404ing when the prefix is unknown or not yet available.
     */
    private function resolveLocale(string $urlLocale): string
    {
        $locale = $this->portal->localeForUrl($urlLocale);

        if ($locale === null || ! $this->portal->hasLocale($locale)) {
            throw new NotFoundHttpException;
        }

        return $locale;
    }
}
