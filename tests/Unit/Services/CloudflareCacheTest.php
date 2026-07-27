<?php

declare(strict_types=1);

use App\Services\CloudflareCache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

it('knows it is configured when both the token and the zone are set', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');

    expect(CloudflareCache::isConfigured())->toBeTrue();
});

it('knows it is not configured when either is missing', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', null);

    expect(CloudflareCache::isConfigured())->toBeFalse();

    config()->set('services.cloudflare.api_token', null);
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');

    expect(CloudflareCache::isConfigured())->toBeFalse();
});

it('asks cloudflare to purge everything in the zone', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);

    expect(CloudflareCache::purgeEverything())->toBeTrue();

    Http::assertSent(function ($request): bool {
        return $request->url() === 'https://api.cloudflare.com/client/v4/zones/zone-central-perk/purge_cache'
            && $request->method() === 'POST'
            && $request->data() === ['purge_everything' => true]
            && $request->hasHeader('Authorization', 'Bearer token-of-the-one-with-the-cache');
    });
});

it('does nothing on an instance that does not sit behind cloudflare', function () {
    config()->set('services.cloudflare.api_token', null);
    config()->set('services.cloudflare.zone_id', null);
    Http::fake();

    expect(CloudflareCache::purgeEverything())->toBeFalse();

    Http::assertNothingSent();
});

it('reports a refused purge rather than throwing', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => false], 403)]);

    expect(CloudflareCache::purgeEverything())->toBeFalse();
});

it('reports an unreachable cloudflare rather than throwing', function () {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
    Http::fake(fn () => throw new ConnectionException('Could not resolve host.'));

    expect(CloudflareCache::purgeEverything())->toBeFalse();
});
