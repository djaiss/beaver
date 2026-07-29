<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('lets a keyboard visitor skip the navigation on a marketing page', function () {
    $this->get(route('marketing.index'))
        ->assertOk()
        ->assertSee('href="#main-content"', false)
        ->assertSee('Skip to content')
        ->assertSee('id="main-content"', false);
});

it('lets a keyboard visitor skip the navigation in the documentation portal', function () {
    $this->get('/en/docs/getting-started/what-is-kollek')
        ->assertOk()
        ->assertSee('href="#main-content"', false)
        ->assertSee('id="main-content"', false);
});

it('lets a keyboard visitor skip the navigation in the api reference', function () {
    $this->get(route('marketing.docs.api.index'))
        ->assertOk()
        ->assertSee('href="#main-content"', false)
        ->assertSee('id="main-content"', false);
});

it('translates the skip link', function () {
    $this->get('/fr/pricing')
        ->assertOk()
        ->assertSee('Aller au contenu');
});
