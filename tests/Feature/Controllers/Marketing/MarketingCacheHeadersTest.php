<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Symfony re-serializes Cache-Control with its directives in alphabetical order,
// so this is the header the middleware's `public, max-age, s-maxage` ends up as.
$expected = 'max-age=300, public, s-maxage=604800, stale-while-revalidate=60';

beforeEach(function (): void {
    config()->set('marketing.show', true);

    // The suite runs with the cache headers off (see phpunit.xml) so nothing has
    // to think about them. These tests opt back in.
    config()->set('marketing.cache_public_pages', true);
});

it('tells the cdn and the browser how long a public page may be reused', function () use ($expected) {
    $this->get('/en/pricing')
        ->assertOk()
        ->assertHeader('Cache-Control', $expected);
});

it('sends the same headers on the legal pages and the docs markdown', function () use ($expected) {
    $this->get('/en/terms')->assertHeader('Cache-Control', $expected);
    $this->get('/en/docs/api.md')->assertHeader('Cache-Control', $expected);
});

it('holds the redirect the bare domain answers with', function () use ($expected) {
    // The most linked URL on the site, and its answer does not change. Without
    // this it is the one public URL that wakes PHP on every single hit.
    $this->get('/')
        ->assertRedirect(route('marketing.index', ['locale' => 'en']))
        ->assertHeader('Cache-Control', $expected);
});

it('never holds the redirect to the login page', function () {
    // An instance with the public site off answers every public URL with a 302
    // to the login page. A shared cache keeping that would serve it to everyone.
    config()->set('marketing.show', false);

    $response = $this->get('/')->assertRedirect(route('login'));

    expect($response->headers->get('Cache-Control'))->not->toContain('s-maxage');
});

it('never lets a page that is not a rendered page be held', function () {
    // Laravel answers with `no-cache, private` of its own accord, so what matters
    // here is only that nothing invites a shared cache to keep it.
    $response = $this->get('/en/features/the-one-that-does-not-exist')->assertNotFound();

    expect($response->headers->get('Cache-Control'))->not->toContain('s-maxage');
});

it('holds nothing on an instance that turns the headers off', function () {
    config()->set('marketing.cache_public_pages', false);

    $response = $this->get('/en/pricing')->assertOk();

    expect($response->headers->get('Cache-Control'))->not->toContain('s-maxage');
});

it('serves a public page without starting a session', function () {
    // A response that carries a cookie is a response a shared cache refuses to
    // hold, which is why the public site runs outside the web middleware group.
    $response = $this->get('/en/pricing')->assertOk();

    expect($response->headers->getCookies())->toBeEmpty();
    $response->assertHeaderMissing('Set-Cookie');
});

it('shows every visitor the same page, signed in or not', function () {
    $rachel = $this->createUser();

    $this->get('/en/pricing')->assertOk()->assertSee('Get started');
    $this->actingAs($rachel)->get('/en/pricing')->assertOk()->assertSee('Get started')->assertDontSee('Go to your account');
});
