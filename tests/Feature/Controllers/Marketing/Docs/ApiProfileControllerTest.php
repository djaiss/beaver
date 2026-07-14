<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the api profile page', function () {
    $response = $this->get('/docs/1.x/api/account/profile');

    $response->assertOk();
});
it('returns the profile document as markdown', function () {
    $response = $this->get('/docs/1.x/api/account/profile.md');

    $response->assertOk();
});
