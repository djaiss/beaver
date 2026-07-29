<?php

declare(strict_types=1);

use App\Models\Testimonial;
use App\Services\DocumentationPortal;
use App\Services\Sitemap;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('describes every entry as a url with its alternates', function () {
    $entries = app(Sitemap::class)->entries();

    expect($entries)->not->toBeEmpty();

    foreach ($entries as $entry) {
        expect($entry)->toHaveKeys(['loc', 'alternates'])
            ->and($entry['loc'])->toStartWith(config('app.url'));

        foreach ($entry['alternates'] as $alternate) {
            expect($alternate)->toHaveKeys(['hreflang', 'url']);
        }
    }
});

it('gives a translated page one entry per language', function () {
    $locations = array_column(app(Sitemap::class)->entries(), 'loc');

    $pricing = array_filter($locations, fn (string $url): bool => str_ends_with($url, '/pricing'));

    expect($pricing)->toHaveCount(7);
});

it('names every language of a page, itself included, plus x-default', function () {
    $entries = app(Sitemap::class)->entries();

    $spanish = collect($entries)->firstWhere('loc', url('/es/faq'));

    expect(array_column($spanish['alternates'], 'hreflang'))
        ->toBe(['en', 'fr-FR', 'es-ES', 'de-DE', 'pt-BR', 'zh-CN', 'ja-JP', 'x-default'])
        ->and(collect($spanish['alternates'])->firstWhere('hreflang', 'x-default')['url'])
        ->toBe(url('/en/faq'));
});

it('claims nothing for a page that is the same english text behind every prefix', function () {
    $entries = collect(app(Sitemap::class)->entries());

    foreach (config('marketing.english_only_routes') as $name) {
        $entry = $entries->firstWhere('loc', route($name, ['locale' => 'en']));

        expect($entry)->not->toBeNull()
            ->and($entry['alternates'])->toBe([]);
    }
});

it('lists the documentation home once rather than twice', function () {
    $locations = array_column(app(Sitemap::class)->entries(), 'loc');

    // The introduction page sits at the root of a locale folder and is served by
    // the portal home route, so listing it again under a section and a slug would
    // put the same page in twice.
    expect(array_filter($locations, fn (string $url): bool => $url === url('/en/docs')))->toHaveCount(1);
});

it('lists every documentation page a language carries', function () {
    $portal = app(DocumentationPortal::class);
    $locations = array_column(app(Sitemap::class)->entries(), 'loc');

    $expected = collect($portal->pagesFor('fr_FR'))
        ->reject(fn (array $page): bool => $page['is_home'])
        ->map(fn (array $page): string => $portal->urlFor('fr_FR', $page));

    expect($expected)->not->toBeEmpty();

    foreach ($expected as $url) {
        expect($locations)->toContain($url);
    }
});

it('holds back the reviews page while nothing is published', function () {
    expect(array_column(app(Sitemap::class)->entries(), 'loc'))
        ->not->toContain(url('/en/testimonials'));

    Testimonial::factory()->published()->create();

    expect(array_column(app(Sitemap::class)->entries(), 'loc'))
        ->toContain(url('/en/testimonials'));
});
