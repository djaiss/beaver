<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Finder\Finder;

/*
 * Reads the Markdown documentation in docs/portal and answers every question
 * the portal controllers ask: which locales exist, the navigation tree for a
 * locale, and how to resolve a page or a @doc() identifier to a URL.
 *
 * Everything is derived from the files on disk. The numeric prefixes on folders
 * and files decide the order, the frontmatter decides the URL and the labels.
 */
class DocumentationPortal
{
    /**
     * The parsed index, keyed by locale. Each locale holds a list of page
     * arrays. Built once per request, and cached in production.
     *
     * @var array<string, array<int, array<string, mixed>>>|null
     */
    private ?array $index = null;

    public function defaultLocale(): string
    {
        return config('docs.default_locale');
    }

    /**
     * The locales that actually have a folder on disk, in configured order.
     *
     * @return array<int, string>
     */
    public function availableLocales(): array
    {
        return collect(config('docs.locales'))
            ->keys()
            ->filter(fn (string $locale): bool => is_dir($this->localePath($locale)))
            ->values()
            ->all();
    }

    public function hasLocale(string $locale): bool
    {
        return in_array($locale, $this->availableLocales(), true);
    }

    /**
     * The home page (the top level Markdown file) for a locale, falling back
     * to the default locale when the locale has no home of its own.
     *
     * @return array{page: array<string, mixed>, fallback: bool}|null
     */
    public function home(string $locale): ?array
    {
        $home = collect($this->pagesFor($locale))->firstWhere('is_home', true);

        if ($home !== null) {
            return ['page' => $home, 'fallback' => false];
        }

        $fallback = collect($this->pagesFor($this->defaultLocale()))->firstWhere('is_home', true);

        return $fallback === null ? null : ['page' => $fallback, 'fallback' => true];
    }

    /**
     * Resolve a page from its locale, section and slug. When the locale does
     * not carry that page, fall back to the default locale and flag it, so the
     * controller can show the "not translated yet" banner.
     *
     * @return array{page: array<string, mixed>, fallback: bool}|null
     */
    public function find(string $locale, string $section, string $slug): ?array
    {
        $page = $this->matchPage($locale, $section, $slug);

        if ($page !== null) {
            return ['page' => $page, 'fallback' => false];
        }

        if ($locale === $this->defaultLocale()) {
            return null;
        }

        $fallback = $this->matchPage($this->defaultLocale(), $section, $slug);

        return $fallback === null ? null : ['page' => $fallback, 'fallback' => true];
    }

    /**
     * The sidebar navigation for a locale: a list of sections, each with its
     * ordered items. Sections and items are ordered by their numeric prefixes.
     *
     * @return array<int, array{title: string, items: array<int, array<string, mixed>>}>
     */
    public function navigation(string $locale): array
    {
        $pages = collect($this->pagesFor($locale))->filter(fn (array $page): bool => ! $page['is_home']);

        return $pages
            ->groupBy('folder')
            ->sortBy(fn (Collection $group): int => $group->first()['section_order'])
            ->map(function (Collection $group) use ($locale): array {
                $items = $group->sortBy('page_order')->values();

                return [
                    'title' => $items->first()['title'],
                    'items' => $items->map(fn (array $page): array => [
                        'id' => $page['id'],
                        'title' => $page['title'],
                        'url' => $this->urlFor($locale, $page),
                    ])->all(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * The URL for a page in a given locale.
     */
    public function urlFor(string $locale, array $page): string
    {
        return route('marketing.docs.portal.show', [
            'locale' => $locale,
            'section' => $page['section'],
            'slug' => $page['slug'],
        ]);
    }

    /**
     * Resolve a @doc() identifier to a URL for the current locale, falling back
     * to the default locale when the page is not translated. Returns null when
     * the identifier is unknown.
     */
    public function urlForId(string $id, string $locale): ?string
    {
        $page = $this->pageById($id, $locale);

        if ($page === null) {
            return null;
        }

        if ($page['is_home']) {
            return route('marketing.docs.portal.home.show', ['locale' => $page['locale']]);
        }

        return $this->urlFor($page['locale'], $page);
    }

    /**
     * The title of the page a @doc() identifier points at, used as the default
     * link label when the directive gives none.
     */
    public function titleForId(string $id, string $locale): ?string
    {
        return $this->pageById($id, $locale)['title'] ?? null;
    }

    public function knowsId(string $id, string $locale): bool
    {
        return $this->pageById($id, $locale) !== null;
    }

    /**
     * All pages for a locale, reading from the cache in production and from
     * disk everywhere else so editing a file shows up immediately.
     *
     * @return array<int, array<string, mixed>>
     */
    public function pagesFor(string $locale): array
    {
        return $this->buildIndex()[$locale] ?? [];
    }

    /**
     * Build (and remember) the whole index for every available locale.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function buildIndex(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        if (app()->isProduction()) {
            return $this->index = Cache::rememberForever('docs.index', fn (): array => $this->scan());
        }

        return $this->index = $this->scan();
    }

    /**
     * Rebuild the cached index from disk. Called by the docs:cache command.
     */
    public function refreshCache(): void
    {
        Cache::forget('docs.index');
        Cache::forever('docs.index', $this->scan());
        $this->index = null;
    }

    /**
     * Scan every locale folder and parse each file's frontmatter into a page.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    private function scan(): array
    {
        $index = [];

        foreach ($this->availableLocales() as $locale) {
            $index[$locale] = $this->scanLocale($locale);
        }

        return $index;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function scanLocale(string $locale): array
    {
        $finder = Finder::create()
            ->files()
            ->in($this->localePath($locale))
            ->name('*.md')
            ->depth('< 2');

        $pages = [];

        foreach ($finder as $file) {
            $meta = $this->frontmatter($file->getContents());

            if ($meta === null) {
                continue;
            }

            $relative = $file->getRelativePathname();
            $folder = str_contains($relative, DIRECTORY_SEPARATOR)
                ? explode(DIRECTORY_SEPARATOR, $relative)[0]
                : null;

            $pages[] = [
                'id' => $meta['id'],
                'title' => $meta['title'],
                'slug' => $meta['slug'],
                'section' => $meta['section'],
                'locale' => $locale,
                'path' => $file->getRealPath(),
                'folder' => $folder,
                'is_home' => $folder === null,
                'section_order' => $folder === null ? 0 : $this->orderPrefix($folder),
                'page_order' => $this->orderPrefix($file->getFilename()),
            ];
        }

        return $pages;
    }

    /**
     * Parse the leading YAML style frontmatter block. The block is flat
     * (key: value per line), so a small hand parser is enough and avoids a
     * dependency. Returns null when the four required keys are not all present.
     *
     * @return array{id: string, title: string, slug: string, section: string}|null
     */
    public function frontmatter(string $raw): ?array
    {
        if (! preg_match('/^(?:\xEF\xBB\xBF)?---\s*\n(.*?)\n---\s*\n/s', $raw, $matches)) {
            return null;
        }

        $meta = [];

        foreach (explode("\n", $matches[1]) as $line) {
            if (! str_contains($line, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $line, 2);
            $meta[trim($key)] = trim(trim($value), " \t\"'");
        }

        foreach (['id', 'title', 'slug', 'section'] as $key) {
            if (empty($meta[$key])) {
                return null;
            }
        }

        return [
            'id' => $meta['id'],
            'title' => $meta['title'],
            'slug' => $meta['slug'],
            'section' => $meta['section'],
        ];
    }

    private function matchPage(string $locale, string $section, string $slug): ?array
    {
        return collect($this->pagesFor($locale))
            ->first(fn (array $page): bool => $page['section'] === $section && $page['slug'] === $slug);
    }

    /**
     * Find a page by identifier, preferring the given locale and falling back
     * to the default locale so links keep working before a page is translated.
     */
    private function pageById(string $id, string $locale): ?array
    {
        return collect($this->pagesFor($locale))->firstWhere('id', $id)
            ?? collect($this->pagesFor($this->defaultLocale()))->firstWhere('id', $id);
    }

    private function orderPrefix(string $name): int
    {
        return preg_match('/^(\d+)/', $name, $matches) ? (int) $matches[1] : 0;
    }

    private function localePath(string $locale): string
    {
        return config('docs.portal_path').DIRECTORY_SEPARATOR.$locale;
    }
}
