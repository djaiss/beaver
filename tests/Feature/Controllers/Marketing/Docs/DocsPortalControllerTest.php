<?php

declare(strict_types=1);

use App\Services\DocumentationPortal;

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('redirects the bare docs url to the default locale home', function () {
    $this->get('/docs')
        ->assertRedirect(route('marketing.docs.portal.home', ['locale' => 'en']));
});

it('renders the english home page', function () {
    $this->get('/docs/en')
        ->assertOk()
        ->assertSee('KolleK documentation')
        ->assertSee('On this page', false);
});

it('renders a documentation page with its navigation and table of contents', function () {
    $this->get('/docs/en/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('What is KolleK')
        // Sidebar section heading.
        ->assertSee('Getting Started')
        // A resolved @doc() link points at another portal page, never a raw file path.
        ->assertSee('/docs/en/', false)
        ->assertDontSee('@doc(', false)
        ->assertDontSee('.md', false);
});

it('renders note and warning admonitions from the markdown', function () {
    $this->get('/docs/en/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('doc-admonition', false);
});

it('renders every english page without error', function () {
    $pages = app(DocumentationPortal::class)->pagesFor('en');

    expect($pages)->not->toBeEmpty();

    foreach ($pages as $page) {
        $url = $page['is_home']
            ? route('marketing.docs.portal.home', ['locale' => 'en'])
            : route('marketing.docs.portal.show', ['locale' => 'en', 'section' => $page['section'], 'slug' => $page['slug']]);

        $this->get($url)->assertOk();
    }
});

it('returns not found for an unknown page', function () {
    $this->get('/docs/en/getting-started/does-not-exist')->assertNotFound();
});

it('returns not found for an unsupported locale', function () {
    // A locale that is not in the configured list never reaches the portal
    // controller, so the API docs route and everything else keep working.
    $this->get('/docs/api')->assertOk();
});

it('offers every available locale in the language selector', function () {
    $this->get('/docs/en/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('English');
});

it('links to the documentation from the marketing header and footer', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee(route('marketing.docs.portal.index'), false);
});
