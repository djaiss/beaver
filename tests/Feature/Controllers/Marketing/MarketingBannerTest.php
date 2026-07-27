<?php

declare(strict_types=1);

use App\Models\SiteOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('shows no announcement banner when nothing has been saved', function () {
    $this->get('/en/pricing')
        ->assertOk()
        ->assertDontSee('Custom item types are here.');
});

it('shows no announcement banner when it is switched off', function () {
    SiteOption::factory()->withBanner()->create(['banner_enabled' => false]);

    $this->get('/en/pricing')
        ->assertOk()
        ->assertDontSee('Custom item types are here.');
});

it('shows the announcement banner with its version and link', function () {
    SiteOption::factory()->withBanner()->create();

    $this->get('/en/pricing')
        ->assertOk()
        ->assertSee('Custom item types are here. Build a schema for any hobby.')
        ->assertSee('v0.9')
        ->assertSee('Read the changelog');
});

it('shows the announcement banner in the language of the page', function () {
    SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_content' => [
            'en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog'],
            'fr_FR' => ['text' => 'Les types sont arrivés.', 'link_label' => 'Lire le journal'],
        ],
    ]);

    $this->get('/fr/pricing')
        ->assertOk()
        ->assertSee('Les types sont arrivés.')
        ->assertDontSee('Custom item types are here.');
});

it('falls back to english for a language that was left empty', function () {
    SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_content' => ['en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog']],
    ]);

    $this->get('/de/pricing')
        ->assertOk()
        ->assertSee('Custom item types are here.');
});
