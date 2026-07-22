<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects the bare root to the default locale home', function () {
    config()->set('marketing.show', true);

    $this->get('/')->assertRedirect(route('marketing.index', ['locale' => 'en']));
});

it('renders the marketing homepage under the locale prefix', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/en');

    $response
        ->assertOk()
        ->assertSee('The collection manager that belongs to you.')
        ->assertSee('One app for every collection.')
        ->assertSee('https://github.com/djaiss/kollek');
});

it('draws an animated icon for each supported collection', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/en');

    $response
        ->assertOk()
        // A named tile for a few of the supported collections, each with its own illustration.
        ->assertSee('Vinyl Records')
        ->assertSee('Trading Cards')
        ->assertSee('And more…')
        // The illustrations are inline SVGs that lift and grow on hover.
        ->assertSee('<svg viewBox="0 0 96 96"', false)
        ->assertSee('group-hover:scale-110', false)
        // Discs turn as they lift.
        ->assertSee('group-hover:rotate-[18deg]', false);
});

it('shows the privacy section above the pricing section', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/en');

    $response
        ->assertOk()
        ->assertSee('Private by default. Not by policy.')
        ->assertSee('Encrypted at rest')
        ->assertSee('Zero tracking')
        ->assertSeeInOrder(['id="privacy"', 'id="pricing"'], false);
});

it('offers a theme toggle in the footer', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/en');

    // The same component the logged in sidebar uses. Which icon shows is driven by the
    // `dark` class that the script in partials/meta sets before paint, and that script
    // falls back to the operating system preference when nothing has been chosen yet.
    $response
        ->assertOk()
        ->assertSee('data-test="theme-toggle"', false)
        ->assertSee('$store.theme.toggle()', false)
        ->assertSee('prefers-color-scheme: dark', false);
});

it('offers to sign up when the visitor is a guest', function () {
    config()->set('marketing.show', true);

    $response = $this->get('/en');

    $response
        ->assertOk()
        ->assertSee('Get started')
        ->assertSee(route('register'));
});

it('offers to go back to the account when the visitor is signed in', function () {
    config()->set('marketing.show', true);
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/en');

    $response
        ->assertOk()
        ->assertSee('Go to your account')
        ->assertSee(route('dashboard.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get('/')->assertRedirect(route('login'));

    $user = $this->createUser();
    $this->actingAs($user)->get('/')->assertRedirect(route('login'));
});

it('carries signed in users on to their dashboard when the marketing site is off', function () {
    config()->set('marketing.show', false);
    $user = $this->createUser();

    // The login page sits behind the guest middleware, so it hands anyone already signed
    // in back to the application instead of leaving them on a redirect loop.
    $response = $this->actingAs($user)->followingRedirects()->get('/');

    $response->assertOk();
});
