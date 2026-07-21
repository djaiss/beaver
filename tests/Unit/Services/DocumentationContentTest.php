<?php

declare(strict_types=1);

use App\Services\DocumentationParser;
use App\Services\DocumentationPortal;
use Symfony\Component\Finder\Finder;

/*
 * These tests guard the documentation content itself: every Markdown file must
 * carry valid frontmatter, every page id must be unique, every @doc() link must
 * resolve, and every translated locale must stay in step with English.
 */

beforeEach(function (): void {
    config()->set('marketing.show', true);
    $this->portal = app(DocumentationPortal::class);
    $this->parser = app(DocumentationParser::class);
});

/**
 * Every Markdown file on disk, per locale, as [locale, relativePath, rawContents].
 *
 * @return array<int, array{0: string, 1: string, 2: string}>
 */
function documentationFiles(): array
{
    $files = [];

    foreach (glob(config('docs.portal_path').'/*', GLOB_ONLYDIR) as $localeDir) {
        $locale = basename($localeDir);

        foreach (Finder::create()->files()->in($localeDir)->name('*.md') as $file) {
            $files[] = [$locale, $file->getRelativePathname(), $file->getContents()];
        }
    }

    return $files;
}

it('has at least the english locale on disk', function () {
    expect($this->portal->availableLocales())->toContain('en');
});

it('gives every file complete frontmatter', function () {
    $missing = [];

    foreach (documentationFiles() as [$locale, $path, $raw]) {
        if ($this->portal->frontmatter($raw) === null) {
            $missing[] = "{$locale}/{$path}";
        }
    }

    expect($missing)->toBe([]);
});

it('keeps every page id unique within a locale', function () {
    foreach ($this->portal->availableLocales() as $locale) {
        $ids = collect($this->portal->pagesFor($locale))->pluck('id');
        $duplicates = $ids->duplicates()->values()->all();

        expect($duplicates)->toBe([], "Duplicate ids in {$locale}: ".implode(', ', $duplicates));
    }
});

it('keeps every section and slug pair unique within a locale', function () {
    foreach ($this->portal->availableLocales() as $locale) {
        $keys = collect($this->portal->pagesFor($locale))
            ->map(fn (array $page): string => $page['section'].'/'.$page['slug']);
        $duplicates = $keys->duplicates()->values()->all();

        expect($duplicates)->toBe([], "Duplicate URLs in {$locale}: ".implode(', ', $duplicates));
    }
});

it('resolves every doc directive to a known page', function () {
    $broken = [];

    foreach (documentationFiles() as [$locale, $path, $raw]) {
        $body = $this->parser->split($raw)['body'];

        foreach ($this->parser->docReferences($body) as $id) {
            if (! $this->portal->knowsId($id, $locale)) {
                $broken[] = "{$locale}/{$path} -> @doc({$id})";
            }
        }
    }

    expect($broken)->toBe([]);
});

it('renders every page without throwing', function () {
    foreach (documentationFiles() as [$locale, $path, $raw]) {
        $body = $this->parser->split($raw)['body'];

        expect(fn () => $this->parser->render($body, $locale))
            ->not->toThrow(Throwable::class, "Failed rendering {$locale}/{$path}");
    }
});

it('keeps every translated locale in step with english', function () {
    $english = collect($this->portal->pagesFor('en'));
    $translated = array_diff($this->portal->availableLocales(), ['en']);

    foreach ($translated as $locale) {
        $pages = collect($this->portal->pagesFor($locale));

        // Same id set, and a shared id keeps the same section and slug so the
        // URL and the language switcher line up across locales.
        expect($pages->pluck('id')->sort()->values()->all())
            ->toBe($english->pluck('id')->sort()->values()->all(), "Page set drift in {$locale}");

        foreach ($pages as $page) {
            $reference = $english->firstWhere('id', $page['id']);

            expect([$page['section'], $page['slug']])
                ->toBe([$reference['section'], $reference['slug']], "URL drift for {$page['id']} in {$locale}");
        }
    }
});
