<?php

declare(strict_types=1);

use App\Services\DocumentationPortal;

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('does not expose an unprefixed docs entry point', function () {
    // The documentation lives under a locale prefix (/en/docs); there is no
    // bare /docs shortcut, so it is simply not found.
    $this->get('/docs')->assertNotFound();
});

it('renders the english docs home under the locale prefix', function () {
    $this->get('/en/docs')
        ->assertOk()
        ->assertSee('KolleK documentation')
        ->assertSee('On this page', false);
});

it('renders a documentation page with its navigation and table of contents', function () {
    $this->get('/en/docs/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('What is KolleK')
        // Sidebar section heading.
        ->assertSee('Getting Started')
        // A resolved @doc() link points at another portal page, never a raw file path.
        ->assertSee('/en/docs/', false)
        ->assertDontSee('@doc(', false)
        ->assertDontSee('.md', false);
});

it('renders note and warning admonitions from the markdown', function () {
    $this->get('/en/docs/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('doc-admonition', false);
});

it('renders every english page without error', function () {
    $pages = app(DocumentationPortal::class)->pagesFor('en');

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $page) {
        $url = $page['is_home']
            ? route('marketing.docs.portal.home.show', ['locale' => 'en'])
            : route('marketing.docs.portal.show', ['locale' => 'en', 'section' => $page['section'], 'slug' => $page['slug']]);

        $this->get($url)->assertOk();
    }
});

it('returns not found for an unknown page', function () {
    $this->get('/en/docs/getting-started/does-not-exist')->assertNotFound();
});

it('returns not found for an unsupported locale prefix', function () {
    // The API reference keeps working under the default locale.
    $this->get('/en/docs/api')->assertOk();
    // A locale prefix that is not offered never matches the localized routes.
    $this->get('/xx')->assertNotFound();
    $this->get('/xx/docs')->assertNotFound();
});

it('offers every available locale in the language selector', function () {
    $this->get('/en/docs/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('English');
});

it('links to the documentation from the marketing header and footer', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee(route('marketing.docs.portal.home.show', ['locale' => 'en']), false);
});

it('renders the french docs home at its short language prefix', function () {
    $this->get('/fr/docs')
        ->assertOk()
        ->assertSee('Documentation KolleK');
});

it('renders every french page at its fully localized section and slug', function () {
    $pages = app(DocumentationPortal::class)->pagesFor('fr_FR');

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $page) {
        $url = $page['is_home']
            ? route('marketing.docs.portal.home.show', ['locale' => 'fr'])
            : route('marketing.docs.portal.show', ['locale' => 'fr', 'section' => $page['section'], 'slug' => $page['slug']]);

        $this->get($url)->assertOk();
    }
});

it('resolves a french page at its translated url instead of the english one', function () {
    $this->get('/fr/docs/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee('Créer votre compte');

    // The English section and slug never resolve under the French prefix: a
    // localized URL is strict, so a reused English path is not found rather than
    // served as duplicate content.
    $this->get('/fr/docs/getting-started/create-your-account')->assertNotFound();

    $this->get('/en/docs/getting-started/create-your-account')->assertOk();
});

it('resolves a doc directive to the current locale translated url', function () {
    // The page body links to gettingStarted.checklist and auth.signIn through
    // @doc(), which must resolve to their French section and slug, not English.
    $this->get('/fr/docs/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee('/fr/docs/demarrage/liste-de-demarrage', false)
        ->assertSee('/fr/docs/demarrage/se-connecter', false);
});

it('points the language switcher at the equivalent page in each locale, not the current section and slug', function () {
    // Regression: the English entry must resolve accounts.create's own
    // English section and slug, not the current French page's.
    $this->get('/fr/docs/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee(route('marketing.docs.portal.show', ['locale' => 'en', 'section' => 'getting-started', 'slug' => 'create-your-account']), false)
        ->assertDontSee('/en/docs/demarrage/', false);
});

it('drives the docs sidebar and content links through turbo', function () {
    $response = $this->get('/en/docs/getting-started/what-is-kollek');

    $response
        ->assertOk()
        // The sidebar navigation and the resolved in content @doc() link both
        // opt into Turbo Drive.
        ->assertSee('data-turbo="true"', false)
        ->assertSee('/en/docs/', false);
});
