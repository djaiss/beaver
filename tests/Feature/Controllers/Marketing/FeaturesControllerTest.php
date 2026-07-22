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
    $this->get(route('marketing.features.show', 'copy-tracking'))
        ->assertOk()
        ->assertSee('Copy tracking')
        ->assertSee('Distinguish, locate, and understand every physical item you own.');
});

it('returns not found for an unknown feature slug', function () {
    $this->get(route('marketing.features.show', 'not-a-real-feature'))->assertNotFound();
});

it('redirects to login when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.features.index'))->assertRedirect(route('login'));
});
