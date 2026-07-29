<?php

declare(strict_types=1);

use App\ViewModels\MarketingFaq;
use App\ViewModels\MarketingFeatures;
use App\ViewModels\MarketingStructuredData;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

/**
 * The graph a given route produces, built the way the view builds it.
 *
 * @return array<string, mixed>
 */
function graphFor(string $routeName, string $uri = '/en/pricing'): array
{
    return new MarketingStructuredData(new MarketingFeatures, new MarketingFaq)
        ->forRequest(requestFor($routeName, $uri));
}

function requestFor(string $routeName, string $uri): Request
{
    $request = Request::create(config('app.url').$uri);

    $route = new Route(['GET'], ltrim($uri, '/'), fn () => null);
    $route->name($routeName);
    $route->bind($request);

    $request->setRouteResolver(fn (): Route => $route);

    return $request;
}

/**
 * Every @id the graph declares, and every @id it points at. A reference to an
 *
 * @id nothing declares refers to nothing, which is the one way a graph like this
 * silently stops meaning anything.
 *
 * @param  array<string, mixed>  $graph
 * @return array{declared: array<int, string>, referenced: array<int, string>}
 */
function graphIds(array $graph): array
{
    $declared = array_values(array_filter(array_map(
        fn (array $node): ?string => $node['@id'] ?? null,
        $graph['@graph'],
    )));

    $referenced = [];

    $walk = function (mixed $node) use (&$walk, &$referenced): void {
        if (! is_array($node)) {
            return;
        }

        if (array_keys($node) === ['@id']) {
            $referenced[] = $node['@id'];

            return;
        }

        foreach ($node as $value) {
            $walk($value);
        }
    };

    $walk($graph['@graph']);

    return ['declared' => $declared, 'referenced' => array_values(array_unique($referenced))];
}

/**
 * @param  array<string, mixed>  $graph
 * @return array<int, string>
 */
function graphTypes(array $graph): array
{
    return array_column($graph['@graph'], '@type');
}

it('describes the site on every page', function () {
    foreach (['marketing.index', 'marketing.terms.index', 'marketing.testimonials.index'] as $route) {
        expect(graphTypes(graphFor($route)))->toContain('Organization')->toContain('WebSite');
    }
});

it('is one graph in the schema.org vocabulary', function () {
    $graph = graphFor('marketing.index');

    expect($graph['@context'])->toBe('https://schema.org')
        ->and($graph['@graph'])->toBeArray()->not->toBeEmpty();
});

it('never points at an id it has not declared', function () {
    $routes = [
        'marketing.index', 'marketing.pricing.index', 'marketing.faq.index',
        'marketing.about.index', 'marketing.features.index', 'marketing.features.show',
        'marketing.terms.index', 'marketing.privacy.index', 'marketing.mediaKit.index',
        'marketing.testimonials.index', 'marketing.docs.api.index',
    ];

    foreach ($routes as $route) {
        $ids = graphIds(graphFor($route));

        expect(array_diff($ids['referenced'], $ids['declared']))->toBe([], "dangling reference on {$route}");
    }
});

it('claims the same site entities whatever page is being read', function () {
    $home = collect(graphFor('marketing.index')['@graph'])->firstWhere('@type', 'Organization');
    $terms = collect(graphFor('marketing.terms.index', '/en/terms')['@graph'])->firstWhere('@type', 'Organization');

    expect($home['@id'])->toBe($terms['@id'])
        ->and($home['@id'])->toEndWith('/#organization');
});

it('describes the product on the pages that are about the product', function () {
    foreach (['marketing.index', 'marketing.pricing.index', 'marketing.features.index', 'marketing.features.show'] as $route) {
        expect(graphTypes(graphFor($route)))->toContain('SoftwareApplication');
    }
});

it('leaves the product off the pages that are not about it', function () {
    foreach (['marketing.terms.index', 'marketing.privacy.index', 'marketing.mediaKit.index', 'marketing.testimonials.index'] as $route) {
        expect(graphTypes(graphFor($route)))->not->toContain('SoftwareApplication');
    }
});

it('prices the product the way the pricing page words it', function () {
    $application = collect(graphFor('marketing.pricing.index')['@graph'])->firstWhere('@type', 'SoftwareApplication');

    expect($application['isAccessibleForFree'])->toBeTrue()
        ->and($application['license'])->toContain('LICENSE')
        ->and(array_column($application['offers'], 'price'))->toBe(['0', (string) config('pricing.price')])
        ->and(array_column($application['offers'], 'priceCurrency'))->toBe(['USD', 'USD']);
});

it('lists every feature area the mega menu lists', function () {
    $application = collect(graphFor('marketing.index')['@graph'])->firstWhere('@type', 'SoftwareApplication');

    expect($application['featureList'])->toBe(array_column(new MarketingFeatures()->all(), 'title'))
        ->and($application['featureList'])->toHaveCount(12);
});

it('names every language the site is served in', function () {
    $website = collect(graphFor('marketing.index')['@graph'])->firstWhere('@type', 'WebSite');

    expect($website['inLanguage'])->toBe(['en', 'fr-FR', 'es-ES', 'de-DE', 'pt-BR', 'zh-CN', 'ja-JP']);
});

it('carries every question on the faq page, and each of them once', function () {
    $faq = collect(graphFor('marketing.faq.index', '/en/faq')['@graph'])->firstWhere('@type', 'FAQPage');

    $questions = array_column($faq['mainEntity'], 'name');

    expect($questions)->toHaveCount(new MarketingFaq()->totalQuestions())
        ->and($questions)->toHaveCount(count(array_unique($questions)))
        ->and($faq['mainEntity'][0]['acceptedAnswer']['@type'])->toBe('Answer')
        ->and($faq['mainEntity'][0]['acceptedAnswer']['text'])->not->toBeEmpty();
});

it('leaves the quick answers out of the faq graph', function () {
    $faq = collect(graphFor('marketing.faq.index', '/en/faq')['@graph'])->firstWhere('@type', 'FAQPage');

    // They ask the same things again in two words, and a page that answers one
    // question twice is a page a crawler is entitled to distrust.
    $questions = array_column($faq['mainEntity'], 'name');

    expect($questions)->not->toContain(new MarketingFaq()->quickAnswers()[0]['question']);
});

it('never builds a rating out of the testimonials', function () {
    $graph = graphFor('marketing.testimonials.index', '/en/testimonials');

    $json = json_encode($graph);

    expect($json)->not->toContain('aggregateRating')
        ->and($json)->not->toContain('"Review"');
});

it('describes a documentation page as a technical article under its trail', function () {
    $page = ['title' => 'A five minute quick start', 'is_home' => false];

    $graph = new MarketingStructuredData(new MarketingFeatures, new MarketingFaq)
        ->forDocumentationPage(
            requestFor('marketing.docs.portal.show', '/en/docs/getting-started/quick-start'),
            $page,
            'The fastest path from an empty account to a catalogued item.',
        );

    expect(graphTypes($graph))->toContain('TechArticle')->toContain('BreadcrumbList');

    $article = collect($graph['@graph'])->firstWhere('@type', 'TechArticle');
    $breadcrumb = collect($graph['@graph'])->firstWhere('@type', 'BreadcrumbList');

    expect($article['headline'])->toBe('A five minute quick start')
        ->and($article['description'])->toBe('The fastest path from an empty account to a catalogued item.')
        ->and(array_column($breadcrumb['itemListElement'], 'position'))->toBe([1, 2])
        ->and($breadcrumb['itemListElement'][1]['name'])->toBe('A five minute quick start');

    // Every item before the last one has to name a URL, which is why the section
    // between them is left out: it is a heading, not a page.
    foreach ($breadcrumb['itemListElement'] as $item) {
        expect($item['item'])->toStartWith('http');
    }
});

it('gives the documentation home no trail of its own', function () {
    $graph = new MarketingStructuredData(new MarketingFeatures, new MarketingFaq)
        ->forDocumentationPage(
            requestFor('marketing.docs.portal.home.show', '/en/docs'),
            ['title' => 'KolleK documentation', 'is_home' => true],
            'Welcome.',
        );

    // A breadcrumb of one item describes nothing.
    expect(graphTypes($graph))->toContain('TechArticle')->not->toContain('BreadcrumbList');
});

it('never points at an id it has not declared on a documentation page', function () {
    $graph = new MarketingStructuredData(new MarketingFeatures, new MarketingFaq)
        ->forDocumentationPage(
            requestFor('marketing.docs.portal.show', '/en/docs/getting-started/quick-start'),
            ['title' => 'A five minute quick start', 'is_home' => false],
            'An excerpt.',
        );

    $ids = graphIds($graph);

    expect(array_diff($ids['referenced'], $ids['declared']))->toBe([]);
});
