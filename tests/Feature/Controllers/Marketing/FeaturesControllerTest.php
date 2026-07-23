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
    $this->get(route('marketing.features.show', 'photos-and-browsing'))
        ->assertOk()
        ->assertSee('Photos & browsing')
        ->assertSee('Manage covers, photo libraries, and visual browsing.');
});

it('renders the dedicated data-ownership page with its own copy', function () {
    $this->get(route('marketing.features.show', 'data-ownership'))
        ->assertOk()
        ->assertSee('Keep your collection where you can point at it.')
        ->assertSee('Sensitive details are not stored as plain text.')
        ->assertSee('Backups are real, not a decorative button.')
        // The real volume/env names an operator backs up.
        ->assertSee('db-data')
        ->assertSee('storage-data')
        ->assertSee('APP_KEY')
        // The claim boundary: no end-to-end encryption, no in-app backup button.
        ->assertSee('You need end-to-end encryption where the operator cannot access application data.')
        ->assertSee('You expect an automated in-app backup button.')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated copy-history page with its own copy', function () {
    $this->get(route('marketing.features.show', 'copy-history'))
        ->assertOk()
        ->assertSee('Keep the good story. Lose the paper chase.')
        ->assertSee('Read the big moments, or every last correction.')
        // Every record type that merges into the timeline is present.
        ->assertSee('Provenance')
        ->assertSee('Insurance')
        ->assertSee('Service')
        ->assertSee('Loan')
        ->assertSee('Move')
        // The interactive control offers both timeline views.
        ->assertSee('Meaningful')
        ->assertSee('Complete')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated custom-catalogues page with its own copy', function () {
    $this->get(route('marketing.features.show', 'custom-catalogues'))
        ->assertOk()
        ->assertSee('Your hobby has jargon. We came prepared.')
        ->assertSee('The fields your hobby actually uses.')
        ->assertSee('The same item page, speaking your hobby.')
        // Both proof vocabularies appear side by side.
        ->assertSee('Amazing Spider-Man #300')
        ->assertSee('Barolo Monfortino 2015')
        // The transparency footer keeps the candid caveat about fixed databases.
        ->assertSee('You want a massive public reference database maintained for you.')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated self-hosting page with its own copy', function () {
    $this->get(route('marketing.features.show', 'self-hosting'))
        ->assertOk()
        ->assertSee('Your own collection app. On your own terms.')
        ->assertSee('The supported path is Docker.')
        ->assertSee('Five parts. That is the whole cast.')
        // The architecture is grounded in the real compose stack.
        ->assertSee('db-data → /var/lib/mysql')
        ->assertSee('storage-data → /var/www/html/storage')
        // No automated backup command is claimed, because none ships.
        ->assertSee('No automated backup command ships yet.', escape: false)
        ->assertDontSee('backup:run')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated collection-insights page with its own copy', function () {
    $this->get(route('marketing.features.show', 'collection-insights'))
        ->assertOk()
        ->assertSee('Finally, numbers that know what they are talking about.')
        ->assertSee('Cost and value are two different numbers.')
        ->assertSee('Value over time, and everything that shapes it.')
        // The claim boundary: figures are derived from records, never a live market feed.
        ->assertSee('Built from your recorded valuations')
        ->assertSee('You need live market prices supplied automatically.')
        // An unvalued copy is not a worthless one.
        ->assertSee('unvalued', escape: false)
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
});

it('renders the dedicated organization page with its own copy', function () {
    $this->get(route('marketing.features.show', 'organization'))
        ->assertOk()
        ->assertSee('Organize it your way. Then find it again.')
        ->assertSee('Browse the way your brain works.')
        ->assertSee('Give things a place.')
        ->assertSee('Keep an eye on the missing pieces.')
        // The claim boundary: the filter is never sold as account-wide search.
        ->assertSee('In-collection · not account-wide search')
        ->assertSee('You need global search across every collection today.')
        // The sibling selector marks the current feature.
        ->assertSee('CURRENT');
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
