<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    config()->set('marketing.show', true);
});

it('shows the features hub with every feature area', function () {
    $this->get(route('marketing.features.index'))
        ->assertOk()
        ->assertSee('Copy tracking')
        ->assertSee('Data ownership')
        ->assertSee('Security');
});

it('shows a single feature page for a known slug', function () {
    $this->get(route('marketing.features.show', 'organization'))
        ->assertOk()
        ->assertSee('Organization')
        ->assertSee('Browse visually or compare precisely with tools that scale.');
});

it('renders the dedicated copy-tracking page with its own copy', function () {
    $this->get(route('marketing.features.show', 'copy-tracking'))
        ->assertOk()
        ->assertSee('Yes, the duplicate is a different creature.')
        ->assertSee('One clean record. Every real object underneath.')
        ->assertSee('Choose a copy. The details follow the object.')
        // Each copy carries its own status, so all three tones appear.
        ->assertSee('On display')
        ->assertSee('Stored')
        ->assertSee('Reading copy')
        // The transparency footer keeps the candid caveat about title-only lists.
        ->assertSee('You only need a list of titles.')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated collaboration page with its own copy', function () {
    $this->get(route('marketing.features.show', 'collaboration'))
        ->assertOk()
        ->assertSee('Everyone can help. Not everyone needs the big red button.')
        ->assertSee('Three roles. No interpretive dance.')
        ->assertSee('Who changed it, and when.')
        // The transparency footer keeps the candid caveat about public links.
        ->assertSee('Visibility settings exist, but public links do not yet.')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('reflects the real role gates in the permissions matrix', function () {
    $response = $this->get(route('marketing.features.show', 'collaboration'))->assertOk();

    // Inviting members is owner only; deleting collections is open to editors too.
    $response->assertSee('Invite & manage members')
        ->assertSee('Create & delete collections')
        // Billing is not shipped, so the page claims account settings instead.
        ->assertDontSee('Billing');
});

it('returns not found for an unknown feature slug', function () {
    $this->get(route('marketing.features.show', 'not-a-real-feature'))->assertNotFound();
});

it('redirects to login when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.features.index'))->assertRedirect(route('login'));
});
