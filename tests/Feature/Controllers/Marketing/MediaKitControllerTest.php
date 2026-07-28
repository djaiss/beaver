<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('renders the media kit', function () {
    $this->get(route('marketing.mediaKit.index'))
        ->assertOk()
        ->assertSee('Everything a journalist needs to write about KolleK.')
        ->assertSee('Descriptions you can reuse')
        ->assertSee('Key facts')
        ->assertSee('Numbers worth printing')
        ->assertSee('Logos')
        ->assertSee('Founder')
        ->assertSee('Links');
});

it('says at the top that the page is english only', function () {
    $this->get(route('marketing.mediaKit.index'))
        ->assertOk()
        ->assertSee('This page is only available in English.');

    // The kit itself is never translated, so a reader on the French site gets the
    // English copy with the notice in their own language.
    $this->get('/fr/media-kit')
        ->assertOk()
        ->assertSee('Cette page n\'est disponible qu\'en anglais.')
        ->assertSee('Everything a journalist needs to write about KolleK.');
});

it('publishes the press address from the configuration', function () {
    config()->set('marketing.press_email', 'press@example.com');

    $this->get(route('marketing.mediaKit.index'))
        ->assertOk()
        ->assertSee('press@example.com')
        ->assertSee('mailto:press@example.com', false);
});

it('sends journalists to github when no press address is configured', function () {
    config()->set('marketing.press_email', null);

    $this->get(route('marketing.mediaKit.index'))
        ->assertOk()
        ->assertDontSee('mailto:', false)
        ->assertSee('Ask on GitHub');
});

it('is linked from the footer', function () {
    $this->get(route('marketing.index'))
        ->assertOk()
        ->assertSee(route('marketing.mediaKit.index'));
});

it('sends everyone to the login page when the marketing site is off', function () {
    config()->set('marketing.show', false);

    $this->get(route('marketing.mediaKit.index'))->assertRedirect(route('login'));
});
