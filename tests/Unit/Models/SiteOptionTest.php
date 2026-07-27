<?php

declare(strict_types=1);

use App\Models\SiteOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns an unsaved instance when nothing has been saved yet', function () {
    $siteOption = SiteOption::current();

    expect($siteOption->exists)->toBeFalse()
        ->and($siteOption->banner_enabled)->toBeFalsy();
});

it('returns the one saved row', function () {
    $saved = SiteOption::factory()->withBanner()->create();

    expect(SiteOption::current()->id)->toBe($saved->id);
});

it('gives the banner of the asked locale', function () {
    $siteOption = SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_version' => 'v0.9',
        'banner_url' => 'https://kollek.test/changelog',
        'banner_content' => [
            'en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog'],
            'fr_FR' => ['text' => 'Les types sont arrivés.', 'link_label' => 'Lire le journal'],
        ],
    ]);

    expect($siteOption->bannerFor('fr_FR'))->toBe([
        'version' => 'v0.9',
        'url' => 'https://kollek.test/changelog',
        'text' => 'Les types sont arrivés.',
        'link_label' => 'Lire le journal',
    ]);
});

it('falls back to english field by field', function () {
    $siteOption = SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_url' => 'https://kollek.test/changelog',
        'banner_content' => [
            'en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog'],
            'ja_JP' => ['text' => 'カスタムタイプが登場。', 'link_label' => ''],
        ],
    ]);

    $banner = $siteOption->bannerFor('ja_JP');

    expect($banner['text'])->toBe('カスタムタイプが登場。')
        ->and($banner['link_label'])->toBe('Read the changelog');

    expect($siteOption->bannerFor('de_DE')['text'])->toBe('Custom item types are here.');
});

it('shows nothing when the banner is switched off', function () {
    $siteOption = SiteOption::factory()->withBanner()->create(['banner_enabled' => false]);

    expect($siteOption->bannerFor('en'))->toBeNull();
});

it('shows nothing when even english has no sentence', function () {
    $siteOption = SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_content' => ['en' => ['text' => '', 'link_label' => 'Read the changelog']],
    ]);

    expect($siteOption->bannerFor('en'))->toBeNull();
});

it('drops a link that is not a safe http url', function () {
    $siteOption = SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_url' => 'javascript:alert(1)',
        'banner_content' => ['en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog']],
    ]);

    $banner = $siteOption->bannerFor('en');

    expect($banner['url'])->toBeNull()
        ->and($banner['link_label'])->toBeNull();
});
