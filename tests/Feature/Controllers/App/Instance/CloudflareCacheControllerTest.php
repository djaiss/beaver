<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
});

it('purges the cloudflare cache from the panel', function () {
    Queue::fake();
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->delete(route('instanceAdmin.siteOptions.cloudflareCache.destroy'))
        ->assertRedirect(route('instanceAdmin.siteOptions.index'))
        ->assertSessionHas('status', 'Cloudflare cache purged successfully');

    Http::assertSentCount(1);
});

it('says so when cloudflare refuses the purge', function () {
    Queue::fake();
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => false], 403)]);
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->delete(route('instanceAdmin.siteOptions.cloudflareCache.destroy'))
        ->assertRedirect(route('instanceAdmin.siteOptions.index'))
        ->assertSessionHas('status', 'Cloudflare refused the purge');
});

it('refuses to purge the cloudflare cache for everybody else', function () {
    Queue::fake();
    Http::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->delete(route('instanceAdmin.siteOptions.cloudflareCache.destroy'))
        ->assertNotFound();

    Http::assertNothingSent();
});
