<?php

declare(strict_types=1);

use App\Models\SiteOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('shows the site options to an instance administrator', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    SiteOption::factory()->create([
        'banner_enabled' => true,
        'banner_content' => ['en' => ['text' => 'The one about the sofa is on its way.', 'link_label' => 'Pivot']],
    ]);

    $this->actingAs($monica)
        ->get(route('instanceAdmin.siteOptions.index'))
        ->assertOk()
        ->assertViewIs('app.instance.siteOptions.index')
        ->assertSee('The one about the sofa is on its way.');
});

it('shows the site options before anything has been saved', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->get(route('instanceAdmin.siteOptions.index'))
        ->assertOk();
});

it('hides the site options from everybody else', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->get(route('instanceAdmin.siteOptions.index'))
        ->assertNotFound();
});

it('saves the banner', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->put(route('instanceAdmin.siteOptions.update'), [
            'banner_enabled' => '1',
            'banner_version' => 'v0.9',
            'banner_url' => 'https://kollek.test/changelog',
            'banner_content' => [
                'en' => ['text' => 'Custom item types are here.', 'link_label' => 'Read the changelog'],
                'fr_FR' => ['text' => 'Les types sont arrivés.', 'link_label' => 'Lire le journal'],
            ],
        ])
        ->assertRedirect(route('instanceAdmin.siteOptions.index'))
        ->assertSessionHas('status');

    $siteOption = SiteOption::current();

    expect($siteOption->banner_enabled)->toBeTrue()
        ->and($siteOption->banner_version)->toBe('v0.9')
        ->and($siteOption->banner_content['fr_FR']['text'])->toBe('Les types sont arrivés.');
});

it('refuses a link that is not a url', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->put(route('instanceAdmin.siteOptions.update'), [
            'banner_enabled' => '1',
            'banner_url' => 'javascript:alert(1)',
        ])
        ->assertSessionHasErrors('banner_url');

    expect(SiteOption::query()->count())->toBe(0);
});

it('refuses to save the banner for everybody else', function () {
    Queue::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->put(route('instanceAdmin.siteOptions.update'), ['banner_enabled' => '1'])
        ->assertNotFound();

    expect(SiteOption::query()->count())->toBe(0);
});
