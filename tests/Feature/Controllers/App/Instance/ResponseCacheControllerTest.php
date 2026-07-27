<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\ResponseCache\Facades\ResponseCache;

uses(RefreshDatabase::class);

it('clears the response cache from the panel', function () {
    Queue::fake();
    ResponseCache::spy();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)
        ->delete(route('instanceAdmin.siteOptions.responseCache.destroy'))
        ->assertRedirect(route('instanceAdmin.siteOptions.index'))
        ->assertSessionHas('status');

    ResponseCache::shouldHaveReceived('clear');
});

it('refuses to clear the response cache for everybody else', function () {
    Queue::fake();
    ResponseCache::spy();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)
        ->delete(route('instanceAdmin.siteOptions.responseCache.destroy'))
        ->assertNotFound();

    ResponseCache::shouldNotHaveReceived('clear');
});
