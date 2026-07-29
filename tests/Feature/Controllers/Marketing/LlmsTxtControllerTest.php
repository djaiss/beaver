<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('marketing.show', true);
});

it('serves llms.txt as plain text', function () {
    $this->get('/llms.txt')
        ->assertOk()
        ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
        ->assertSee('# '.config('app.name'), false)
        ->assertSee('## Documentation', false);
});

it('is not served when the marketing site is switched off', function () {
    config()->set('marketing.show', false);

    $this->get('/llms.txt')->assertRedirect(route('login'));
});

it('is held by the cdn like every other public page', function () {
    config()->set('marketing.cache_public_pages', true);

    $this->get('/llms.txt')
        ->assertOk()
        ->assertHeader('Cache-Control', 'max-age=300, public, s-maxage=604800, stale-while-revalidate=60');
});
