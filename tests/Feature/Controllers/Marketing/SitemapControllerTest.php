<?php

declare(strict_types=1);

use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

/**
 * The path of every <loc> in the sitemap, in order.
 *
 * Paths rather than whole URLs: inside a test request the generator builds them
 * against the request host, and these assertions are about which pages are
 * listed rather than about which hostname the suite runs under.
 *
 * @return array<int, string>
 */
function sitemapPaths(string $xml): array
{
    $document = new SimpleXMLElement($xml);

    return array_map(
        fn (SimpleXMLElement $url): string => (string) parse_url((string) $url->loc, PHP_URL_PATH),
        iterator_to_array($document->url, false),
    );
}

/**
 * The <url> element for one path, with its alternates read as hreflang => path.
 *
 * @return array<string, string>
 */
function sitemapAlternates(string $xml, string $path): array
{
    $document = new SimpleXMLElement($xml);

    $entry = collect(iterator_to_array($document->url, false))
        ->first(fn (SimpleXMLElement $url): bool => parse_url((string) $url->loc, PHP_URL_PATH) === $path);

    expect($entry)->not->toBeNull();

    $alternates = [];

    foreach ($entry->children('xhtml', true) as $link) {
        $hreflang = (string) $link->attributes()->hreflang;
        $alternates[$hreflang] = (string) parse_url((string) $link->attributes()->href, PHP_URL_PATH);
    }

    return $alternates;
}

it('serves the sitemap as xml', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
        ->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false)
        ->assertSee('http://www.sitemaps.org/schemas/sitemap/0.9', false);
});

it('is well formed xml', function () {
    $xml = $this->get('/sitemap.xml')->getContent();

    expect(fn (): SimpleXMLElement => new SimpleXMLElement($xml))->not->toThrow(Exception::class);
});

it('is held by the cdn like every other public page', function () {
    config()->set('marketing.cache_public_pages', true);

    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=300, public, s-maxage=604800, stale-while-revalidate=60');
});

it('is not served when the marketing site is switched off', function () {
    config()->set('marketing.show', false);

    $this->get('/sitemap.xml')->assertRedirect(route('login'));
});

it('lists the homepage in every language', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    foreach (['/en', '/fr', '/es', '/de', '/pt', '/zh', '/ja'] as $path) {
        expect($paths)->toContain($path);
    }
});

it('lists every feature page in every language', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    expect($paths)
        ->toContain('/en/features')
        ->toContain('/en/features/copy-tracking')
        ->toContain('/fr/features/copy-tracking')
        ->toContain('/ja/features/self-hosting');
});

it('lists documentation pages under the slug each language gives them', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    expect($paths)
        ->toContain('/en/docs')
        ->toContain('/fr/docs')
        ->toContain('/en/docs/getting-started/quick-start');

    // A translated page keeps its own section and slug, so the French URL is not
    // the English one with another prefix in front of it.
    $french = array_filter($paths, fn (string $path): bool => str_starts_with($path, '/fr/docs/'));

    expect($french)->not->toBeEmpty()
        ->and($paths)->not->toContain('/fr/docs/getting-started/quick-start');
});

it('lists an english only page once, at its english url', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    foreach (['terms', 'privacy', 'media-kit', 'docs/api'] as $page) {
        expect($paths)->toContain('/en/'.$page);

        foreach (['fr', 'es', 'de', 'pt', 'zh', 'ja'] as $prefix) {
            expect($paths)->not->toContain('/'.$prefix.'/'.$page);
        }
    }
});

it('never lists the same url twice', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    expect($paths)->toHaveCount(count(array_unique($paths)));
});

it('carries the hreflang alternates of the page, plus x-default', function () {
    $alternates = sitemapAlternates($this->get('/sitemap.xml')->getContent(), '/fr/pricing');

    expect($alternates)->toBe([
        'en' => '/en/pricing',
        'fr-FR' => '/fr/pricing',
        'es-ES' => '/es/pricing',
        'de-DE' => '/de/pricing',
        'pt-BR' => '/pt/pricing',
        'zh-CN' => '/zh/pricing',
        'ja-JP' => '/ja/pricing',
        'x-default' => '/en/pricing',
    ]);
});

it('claims no alternates for an english only page', function () {
    expect(sitemapAlternates($this->get('/sitemap.xml')->getContent(), '/en/terms'))->toBe([]);
});

it('leaves the reviews page out until there is something published to read', function () {
    expect(sitemapPaths($this->get('/sitemap.xml')->getContent()))->not->toContain('/en/testimonials');

    Testimonial::factory()->published()->create();

    expect(sitemapPaths($this->get('/sitemap.xml')->getContent()))
        ->toContain('/en/testimonials')
        ->toContain('/fr/testimonials');
});

it('lists no url that does not resolve to a page', function () {
    $paths = sitemapPaths($this->get('/sitemap.xml')->getContent());

    // Requesting nine hundred pages would make the suite crawl, so this walks the
    // router instead: every entry has to match a registered marketing route with
    // the parameters it carries.
    foreach ($paths as $path) {
        $route = Route::getRoutes()->match(Request::create($path, 'GET'));

        expect($route->getName())->toStartWith('marketing.');
    }
});
