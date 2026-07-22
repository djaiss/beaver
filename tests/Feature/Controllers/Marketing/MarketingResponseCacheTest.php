<?php

declare(strict_types=1);

use Spatie\ResponseCache\Facades\ResponseCache;

beforeEach(function (): void {
    config()->set('marketing.show', true);

    // The suite runs with the response cache off (see phpunit.xml) so pages stay
    // fresh between tests. These tests opt back in and turn on the debug header
    // so we can read whether a response was served from cache.
    config()->set('responsecache.enabled', true);
    config()->set('responsecache.debug.enabled', true);

    ResponseCache::clear();
});

afterEach(function (): void {
    ResponseCache::clear();
});

it('serves the second request to a marketing page from the response cache', function () {
    $this->get('/en/pricing')
        ->assertOk()
        ->assertHeader('X-Cache-Status', 'MISS');

    $this->get('/en/pricing')
        ->assertOk()
        ->assertHeader('X-Cache-Status', 'HIT');
});

it('caches a guest and a signed in visitor separately', function () {
    $user = $this->createUser();

    // Prime the cache as a guest, then a signed in visitor still misses because
    // the cache key is suffixed with the authenticated user id.
    $this->get('/en/pricing')->assertHeader('X-Cache-Status', 'MISS');

    $this->actingAs($user)->get('/en/pricing')->assertHeader('X-Cache-Status', 'MISS');
});
