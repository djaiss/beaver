<?php

declare(strict_types=1);
use App\Services\DocumentationPortal;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('offers every language the site has content for', function () {
    $response = $this->get('/en/pricing');

    $response->assertOk();

    foreach (config('docs.locales') as $meta) {
        $response->assertSee($meta['label'], false);
    }
});

it('keeps the visitor on the page they are reading when they switch language', function () {
    $this->get('/en/pricing')
        ->assertOk()
        ->assertSee(url('/fr/pricing'), false)
        ->assertSee(url('/ja/pricing'), false);

    $this->get('/en/features/security')
        ->assertOk()
        ->assertSee(url('/de/features/security'), false);
});

it('follows a documentation page to its translated url, not to the english one', function () {
    $portal = app(DocumentationPortal::class);

    $english = $portal->urlForId('items.itemsVsCopies', 'en');
    $french = $portal->urlForId('items.itemsVsCopies', 'fr_FR');

    // The section and the slug are translated per locale, so the French URL is not the
    // English one with another prefix, and swapping the prefix alone would 404.
    expect($french)->not->toBe(str_replace('/en/', '/fr/', (string) $english));

    $this->get($english)
        ->assertOk()
        ->assertSee($french, false);
});

it('marks the language being read', function () {
    $this->get('/fr/pricing')
        ->assertOk()
        ->assertSee('lang="fr-FR"', false);
});
