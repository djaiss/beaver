<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\Finder\Finder;

/*
 * Reads the short help snippets in docs/help, the text shown in the little "?"
 * popovers next to labels and fields across the app. Each snippet is a small
 * Markdown file: an id and a title in the frontmatter, an optional doc
 * identifier that becomes a "Read more" link through the documentation portal,
 * and a body that is the blurb itself.
 *
 * Snippets live at their own granularity, one file per "?", independent of
 * whether a full documentation page exists for the concept. A snippet only
 * links onwards when it names a doc it can point at.
 */
class HelpSnippets
{
    /**
     * The parsed snippets, keyed by locale then by id. Built once per request.
     *
     * @var array<string, array<string, array<string, mixed>>>|null
     */
    private ?array $index = null;

    public function __construct(
        private DocumentationPortal $portal,
    ) {}

    /**
     * Resolve a snippet for a locale, falling back to the default locale when
     * it has not been translated. Returns null when the id is unknown, so the
     * caller can render nothing rather than a broken button.
     *
     * @return array{kicker: ?string, title: string, body: string, url: ?string, note: array{title: string, text: string}|null}|null
     */
    public function find(string $id, string $locale): ?array
    {
        $snippet = $this->snippetFor($id, $locale);

        if ($snippet === null) {
            return null;
        }

        return [
            'kicker' => $snippet['kicker'],
            'title' => $snippet['title'],
            'body' => $snippet['body'],
            'url' => $snippet['doc'] === null ? null : $this->portal->urlForId($snippet['doc'], $locale),
            'note' => $snippet['note'] === null ? null : [
                'title' => $snippet['note_title'] ?? '',
                'text' => $snippet['note'],
            ],
        ];
    }

    public function knows(string $id, string $locale): bool
    {
        return $this->snippetFor($id, $locale) !== null;
    }

    /**
     * Find a snippet by id, preferring the given locale and falling back to the
     * default locale so a "?" keeps working before its snippet is translated.
     *
     * @return array<string, mixed>|null
     */
    private function snippetFor(string $id, string $locale): ?array
    {
        $index = $this->buildIndex();

        return $index[$locale][$id] ?? $index[$this->defaultLocale()][$id] ?? null;
    }

    private function defaultLocale(): string
    {
        return config('docs.default_locale');
    }

    /**
     * Build (and remember for the request) the snippets for every locale. The
     * files are few and small, so a plain per request scan is enough and avoids
     * a cache to invalidate on deploy.
     *
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function buildIndex(): array
    {
        if ($this->index !== null) {
            return $this->index;
        }

        return $this->index = $this->scan();
    }

    /**
     * @return array<string, array<string, array<string, mixed>>>
     */
    private function scan(): array
    {
        $root = config('docs.help_path');

        if (! is_dir($root)) {
            return [];
        }

        $index = [];

        foreach (array_keys(config('docs.locales')) as $locale) {
            $path = $root.DIRECTORY_SEPARATOR.$locale;

            if (! is_dir($path)) {
                continue;
            }

            $index[$locale] = $this->scanLocale($path);
        }

        return $index;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function scanLocale(string $path): array
    {
        $finder = Finder::create()
            ->files()
            ->in($path)
            ->name('*.md')
            ->depth('< 1');

        $snippets = [];

        foreach ($finder as $file) {
            $snippet = $this->parse($file->getContents());

            if ($snippet === null) {
                continue;
            }

            $snippets[$snippet['id']] = $snippet;
        }

        return $snippets;
    }

    /**
     * Split the flat frontmatter from the body. The frontmatter carries an id
     * and a title, an optional kicker (the small uppercase label above the
     * title), an optional doc identifier for the "Read more" link, and an
     * optional note (a short callout). The body is the blurb. The block is flat
     * (key: value per line), so a small hand parser is enough, the same way the
     * documentation portal reads its own frontmatter. Returns null when a
     * required key is missing.
     *
     * @return array{id: string, title: string, kicker: ?string, doc: ?string, note_title: ?string, note: ?string, body: string}|null
     */
    private function parse(string $raw): ?array
    {
        if (! preg_match('/^(?:\xEF\xBB\xBF)?---\s*\n(.*?)\n---\s*\n(.*)$/s', $raw, $matches)) {
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

        if (empty($meta['id']) || empty($meta['title'])) {
            return null;
        }

        return [
            'id' => $meta['id'],
            'title' => $meta['title'],
            'kicker' => empty($meta['kicker']) ? null : $meta['kicker'],
            'doc' => empty($meta['doc']) ? null : $meta['doc'],
            'note_title' => empty($meta['note_title']) ? null : $meta['note_title'],
            'note' => empty($meta['note']) ? null : $meta['note'],
            'body' => trim($matches[2]),
        ];
    }
}
