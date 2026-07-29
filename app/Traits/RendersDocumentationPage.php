<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Shared by the documentation portal controllers, which all resolve a page
 * and render it inside the same three panel shell. The app locale is already
 * set from the URL prefix by the marketing.locale middleware.
 */
trait RendersDocumentationPage
{
    /**
     * @param  array{page: array<string, mixed>, fallback: bool}  $resolved
     */
    private function renderPage(string $locale, array $resolved): View
    {
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
            'languageUrls' => $this->languageUrls($locale, $page),
            'excerpt' => $this->excerpt($parts['body']),
            'markdown' => $this->parser->resolveDocLinks($parts['body'], $locale),
            'markdownUrl' => $this->portal->markdownUrlFor($locale, $page),
        ]);
    }

    /**
     * The same page's Markdown source, with its @doc() references resolved to
     * real links, served plain for the "View as Markdown" link and for an
     * assistant that would rather not parse the rendered HTML.
     *
     * @param  array{page: array<string, mixed>, fallback: bool}  $resolved
     */
    private function renderMarkdown(string $locale, array $resolved): Response
    {
        $page = $resolved['page'];
        $parts = $this->parser->split(file_get_contents($page['path']));
        $body = $this->parser->resolveDocLinks($parts['body'], $locale);

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * The opening sentences of a page, used as its meta and Open Graph
     * description. The body is already in memory here, so this costs nothing,
     * and it beats repeating one generic line across a hundred pages.
     */
    private function excerpt(string $body): string
    {
        $paragraphs = preg_split('/\n{2,}/', trim($body)) ?: [];

        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);

            // Skip everything that is not prose: the h1, note blocks, lists,
            // tables, code fences and the @doc() directives around them.
            if ($paragraph === '' || preg_match('/^([#>\-*|`:]|\d+\.)/', $paragraph) === 1) {
                continue;
            }

            $text = preg_replace('/@doc\([^,)]+,\s*"([^"]+)"\)/', '$1', $paragraph) ?? $paragraph;
            $text = preg_replace('/@doc\(([^)]+)\)/', '', $text) ?? $text;
            $text = trim(preg_replace('/[*_`\[\]]|\(https?:[^)]*\)/', '', $text) ?? $text);
            $text = trim((string) preg_replace('/\s+/', ' ', $text));

            if ($text !== '') {
                return Str::limit($text, 200);
            }
        }

        return (string) config('app.description');
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
                'url' => $this->portal->urlForId($page['id'], $locale) ?? route('marketing.docs.portal.home.show', ['locale' => $meta['url']]),
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
}
