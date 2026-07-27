<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the privacy policy', function () {
    config()->set('marketing.show', true);

    $this->get(route('marketing.privacy.index'))
        ->assertOk()
        ->assertSee('Privacy Policy')
        ->assertSee('Last updated: July 27, 2026')
        ->assertSee('Encryption and Keys');
});

it('tells a visitor reading in another language that the text is English only', function () {
    config()->set('marketing.show', true);

    $this->get('/fr/privacy')
        ->assertOk()
        ->assertSee("Cette page n'est disponible qu'en anglais.");
});

it('stays readable when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.privacy.index'))->assertOk();
});
