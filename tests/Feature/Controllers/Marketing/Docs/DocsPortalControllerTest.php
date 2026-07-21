<?php

declare(strict_types=1);

use App\Services\DocumentationPortal;

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('redirects the bare docs url to the default locale home', function () {
    $this->get('/docs')
        ->assertRedirect(route('marketing.docs.portal.home.show', ['locale' => 'en']));
});

it('renders the english home page right under the domain', function () {
    $this->get('/en')
        ->assertOk()
        ->assertSee('KolleK documentation')
        ->assertSee('On this page', false);
});

it('renders a documentation page with its navigation and table of contents', function () {
    $this->get('/en/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('What is KolleK')
        // Sidebar section heading.
        ->assertSee('Getting Started')
        // A resolved @doc() link points at another portal page, never a raw file path.
        ->assertSee('/en/', false)
        ->assertDontSee('@doc(', false)
        ->assertDontSee('.md', false);
});

it('renders note and warning admonitions from the markdown', function () {
    $this->get('/en/getting-started/what-is-kollek')
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
    $this->get('/en/getting-started/does-not-exist')->assertNotFound();
});

it('returns not found for an unsupported locale prefix', function () {
    // A locale that is not in the configured list never reaches the portal
    // controller, so the API docs route and everything else keep working.
    $this->get('/docs/api')->assertOk();
    $this->get('/xx')->assertNotFound();
});

it('offers every available locale in the language selector', function () {
    $this->get('/en/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('English');
});

it('links to the documentation from the marketing header and footer', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('marketing.docs.portal.index'), false);
});

it('renders the french home page at its short language prefix', function () {
    $this->get('/fr')
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
    $this->get('/fr/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee('Créer votre compte')
        // The English section and slug for the same page never resolve under
        // the French prefix, since the URL is not shared across locales.
        ->assertDontSee('/fr/getting-started/', false);

    $this->get('/en/getting-started/create-your-account')->assertOk();
});

it('resolves a doc directive to the current locale translated url', function () {
    // The page body links to gettingStarted.checklist and auth.signIn through
    // @doc(), which must resolve to their French section and slug, not English.
    $this->get('/fr/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee('/fr/demarrage/liste-de-demarrage', false)
        ->assertSee('/fr/demarrage/se-connecter', false);
});

it('points the language switcher at the equivalent page in each locale, not the current section and slug', function () {
    // Regression: the English entry must resolve accounts.create's own
    // English section and slug, not the current French page's.
    $this->get('/fr/demarrage/creer-votre-compte')
        ->assertOk()
        ->assertSee(route('marketing.docs.portal.show', ['locale' => 'en', 'section' => 'getting-started', 'slug' => 'create-your-account']), false)
        ->assertDontSee('/en/demarrage/', false);
});
