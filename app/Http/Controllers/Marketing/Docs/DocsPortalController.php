<?php

declare(strict_types=1);

namespace App\Http\Controllers\Marketing\Docs;

use App\Http\Controllers\Controller;
use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DocsPortalController extends Controller
{
    public function __construct(
        private DocumentationPortal $portal,
        private DocumentationParser $parser,
    ) {}

    /**
     * Send the bare /docs URL to the default locale home page.
     */
    public function index(): RedirectResponse
    {
        return redirect()->route('marketing.docs.portal.home', [
            'locale' => $this->portal->defaultLocale(),
        ]);
    }

    /**
     * The locale home page, rendered from the top level Markdown file.
     */
    public function home(string $locale): View
    {
        $this->guardLocale($locale);

        $resolved = $this->portal->home($locale);

        if ($resolved === null) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }

    /**
     * A single documentation page.
     */
    public function show(string $locale, string $section, string $slug): View
    {
        $this->guardLocale($locale);

        $resolved = $this->portal->find($locale, $section, $slug);

        if ($resolved === null) {
            throw new NotFoundHttpException;
        }

        return $this->renderPage($locale, $resolved);
    }

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
                ? route('marketing.docs.portal.home', ['locale' => $locale])
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
