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

            $url = $page['is_home']
                ? route('marketing.docs.portal.home.show', ['locale' => $locale])
                : $this->portal->urlFor($locale, $page);

            $links[] = [
                'locale' => $locale,
                'code' => $meta['code'],
                'label' => $meta['label'],
                'flag' => $meta['flag'],
                'url' => $url,
                'translated' => $this->pageExistsIn($locale, $page),
                'current' => $locale === $current,
            ];
        }

        return $links;
    }

    private function pageExistsIn(string $locale, array $page): bool
    {
        if ($page['is_home']) {
            return $this->portal->home($locale) !== null && ! $this->portal->home($locale)['fallback'];
        }

        $found = $this->portal->find($locale, $page['section'], $page['slug']);

        return $found !== null && ! $found['fallback'];
    }

    private function guardLocale(string $locale): void
    {
        if (! $this->portal->hasLocale($locale)) {
            throw new NotFoundHttpException;
        }
    }
}
