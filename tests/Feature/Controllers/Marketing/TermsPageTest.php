<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the terms of use', function () {
    config()->set('marketing.show', true);

    $this->get(route('marketing.terms.index'))
        ->assertOk()
        ->assertSee('Terms of Use')
        ->assertSee('Last updated: July 27, 2026')
        ->assertSee('Collection Records Are Your Responsibility');
});

it('tells a visitor reading in another language that the text is English only', function () {
    config()->set('marketing.show', true);

    $this->get('/fr/terms')
        ->assertOk()
        ->assertSee("Cette page n'est disponible qu'en anglais.");

    $this->get('/en/terms')
        ->assertOk()
        ->assertDontSee('This page is only available in English.');
});

it('stays readable when the marketing site is off', function () {
    // The registration form asks people to agree before signing up, so a self
    // hosted instance without the public site still has to serve this page.
    config()->set('marketing.show', false);

    $this->get(route('marketing.terms.index'))->assertOk();
});
