<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the docs index', function () {
    $response = $this->get('/docs');

    $response->assertOk();
});

it('shows a markdown doc page', function () {
    $response = $this->get('/docs/1.x/organizations/index');

    $response->assertOk();
});

it('returns 404 for directory only path', function () {
    $response = $this->get('/docs/1.x/organizations');

    $response->assertNotFound();
});

it('returns 404 for unknown path', function () {
    $response = $this->get('/docs/1.x/nonexistent-page');

    $response->assertNotFound();
});
