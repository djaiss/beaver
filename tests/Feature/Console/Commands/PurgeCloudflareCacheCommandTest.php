<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;

it('purges the cloudflare cache', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

    $this->artisan('kollek:purge-cloudflare-cache')
        ->expectsOutputToContain('Cloudflare cache purged')
        ->assertSuccessful();
});

it('fails when the instance does not sit behind cloudflare', function () {
    config()->set('services.cloudflare.api_token', null);
    config()->set('services.cloudflare.zone_id', null);
    Http::fake();

    $this->artisan('kollek:purge-cloudflare-cache')
        ->expectsOutputToContain('Cloudflare is not configured')
        ->assertFailed();

    Http::assertNothingSent();
});

it('fails when cloudflare refuses the purge', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => false], 403)]);

    $this->artisan('kollek:purge-cloudflare-cache')
        ->expectsOutputToContain('Cloudflare refused the purge')
        ->assertFailed();
});
