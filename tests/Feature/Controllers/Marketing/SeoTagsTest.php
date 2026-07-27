<?php

declare(strict_types=1);
use App\Services\DocumentationPortal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('describes a page with its own title, description and card', function () {
    $this->get('/en/pricing')
        ->assertOk()
        ->assertSee('<title>Pricing · '.config('app.name').'</title>', false)
        ->assertSee('<meta property="og:title" content="Pricing" />', false)
        ->assertSee('Self-host KolleK for free, forever, under the MIT licence.', false)
        ->assertSee('<meta property="og:type" content="website" />', false)
        ->assertSee('<meta name="twitter:card" content="summary_large_image" />', false)
        ->assertSee(url('/images/og/default.png'), false);
});

it('describes a feature page from the feature catalogue', function () {
    $this->get('/en/features/security')
        ->assertOk()
        ->assertSee('<meta property="og:title" content="Security" />', false)
        ->assertSee('Encryption at rest, 2FA, magic links, recovery codes, and alerts.', false);
});

it('points a translated page at itself and lists every language', function () {
    $response = $this->get('/fr/faq');

    $response
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.url('/fr/faq').'" />', false)
        ->assertSee('<meta property="og:locale" content="fr_FR" />', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/en/faq').'" />', false);

    foreach (['en' => '/en/faq', 'fr-FR' => '/fr/faq', 'es-ES' => '/es/faq', 'de-DE' => '/de/faq', 'pt-BR' => '/pt/faq', 'zh-CN' => '/zh/faq', 'ja-JP' => '/ja/faq'] as $hreflang => $path) {
        $response->assertSee('<link rel="alternate" hreflang="'.$hreflang.'" href="'.url($path).'" />', false);
    }
});

// The terms, the privacy policy, the media kit and the API reference serve the very
// same English text under all seven prefixes. One URL owns them, and none of them
// claims a language, so they cannot compete with each other in search.
it('hands an english only page to its english url and claims no language', function (string $path, string $english) {
    $response = $this->get($path);

    $response
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.url($english).'" />', false)
        ->assertDontSee('hreflang', false)
        ->assertDontSee('og:locale:alternate', false);
})->with([
    ['/fr/terms', '/en/terms'],
    ['/de/privacy', '/en/privacy'],
    ['/ja/media-kit', '/en/media-kit'],
    ['/es/docs/api', '/en/docs/api'],
]);

it('follows a documentation page to its translated slug, not to a swapped prefix', function () {
    $portal = app(DocumentationPortal::class);

    $english = (string) $portal->urlForId('items.itemsVsCopies', 'en');
    $french = (string) $portal->urlForId('items.itemsVsCopies', 'fr_FR');

    $this->get($french)
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.$french.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="en" href="'.$english.'" />', false)
        ->assertSee('<link rel="alternate" hreflang="x-default" href="'.$english.'" />', false);
});

it('describes a documentation page with its opening lines', function () {
    $this->get('/en/docs/core-concepts/items-and-copies')
        ->assertOk()
        ->assertSee('<title>Items versus copies — documentation · '.config('app.name').'</title>', false)
        ->assertSee('This is the most important page in the documentation.', false);
});

it('leaves the application and the error pages out of it', function () {
    $this->get('/login')
        ->assertOk()
        ->assertDontSee('og:title', false)
        ->assertDontSee('rel="canonical"', false);
});
