<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

/**
 * The one JSON-LD graph a page carries, decoded. Fails the test if the page
 * carries none, more than one, or something that is not JSON.
 *
 * @return array<string, mixed>
 */
function graphOn(string $html): array
{
    preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

    expect($matches[1])->toHaveCount(1);

    $graph = json_decode($matches[1][0], true);

    expect(json_last_error())->toBe(JSON_ERROR_NONE, json_last_error_msg());

    return $graph;
}

/**
 * @return array<int, string>
 */
function typesOn(string $html): array
{
    return array_column(graphOn($html)['@graph'], '@type');
}

it('puts one valid graph in the head of every kind of public page', function (string $path, array $expected) {
    $types = typesOn($this->get($path)->assertOk()->getContent());

    expect($types)->toContain('Organization')->toContain('WebSite');

    foreach ($expected as $type) {
        expect($types)->toContain($type);
    }
})->with([
    'home' => ['/en', ['SoftwareApplication']],
    'pricing' => ['/en/pricing', ['SoftwareApplication']],
    'features hub' => ['/en/features', ['SoftwareApplication']],
    'feature page' => ['/en/features/copy-tracking', ['SoftwareApplication']],
    'faq' => ['/en/faq', ['SoftwareApplication', 'FAQPage']],
    'about' => ['/en/about', ['AboutPage']],
    'documentation home' => ['/en/docs', ['TechArticle']],
    'documentation page' => ['/en/docs/getting-started/quick-start', ['TechArticle', 'BreadcrumbList']],
    'api reference' => ['/en/docs/api', []],
    'terms' => ['/en/terms', []],
    'privacy' => ['/en/privacy', []],
    'media kit' => ['/en/media-kit', []],
    'reviews' => ['/en/testimonials', []],
]);

it('follows the language the page is being read in', function () {
    $graph = graphOn($this->get('/fr/faq')->assertOk()->getContent());

    $faq = collect($graph['@graph'])->firstWhere('@type', 'FAQPage');

    expect($faq['inLanguage'])->toBe('fr-FR')
        ->and($faq['mainEntity'][0]['name'])->not->toBe(
            collect(graphOn($this->get('/en/faq')->getContent())['@graph'])
                ->firstWhere('@type', 'FAQPage')['mainEntity'][0]['name']
        );
});

it('walks a documentation page under the documentation home of its own language', function () {
    $graph = graphOn($this->get('/en/docs/getting-started/quick-start')->assertOk()->getContent());

    $breadcrumb = collect($graph['@graph'])->firstWhere('@type', 'BreadcrumbList');

    expect($breadcrumb['itemListElement'])->toHaveCount(2)
        ->and($breadcrumb['itemListElement'][0]['item'])->toEndWith('/en/docs')
        ->and($breadcrumb['itemListElement'][1]['item'])->toEndWith('/en/docs/getting-started/quick-start');
});

it('describes the documentation page it is actually serving', function () {
    $graph = graphOn($this->get('/en/docs/getting-started/quick-start')->assertOk()->getContent());

    $article = collect($graph['@graph'])->firstWhere('@type', 'TechArticle');

    expect($article['headline'])->toBe('A five minute quick start')
        ->and($article['description'])->not->toBeEmpty()
        ->and($article['url'])->toEndWith('/en/docs/getting-started/quick-start');
});

it('anchors the graph to the same host the page builds its other urls with', function () {
    $html = $this->get('/en')->assertOk()->getContent();

    preg_match('#<link rel="canonical" href="([^"]+)"#', $html, $canonical);

    $organization = collect(graphOn($html)['@graph'])->firstWhere('@type', 'Organization');

    $host = parse_url($canonical[1], PHP_URL_SCHEME).'://'.parse_url($canonical[1], PHP_URL_HOST);

    expect($organization['@id'])->toBe($host.'/#organization')
        ->and($organization['logo']['url'])->toStartWith($host);
});

it('escapes a closing tag rather than ending the script early', function () {
    // Every answer on the FAQ page is translated prose. One of them containing a
    // closing tag would end the block and spill the rest onto the page.
    $html = $this->get('/en/faq')->assertOk()->getContent();

    expect($html)->not->toContain('</script></script>');

    graphOn($html);
});

it('leaves the dead json-ld hook behind', function () {
    // The layout carried @yield('json-ld') and no page ever defined the section.
    expect(file_get_contents(resource_path('views/layouts/marketing.blade.php')))
        ->not->toContain("@yield('json-ld')");
});
