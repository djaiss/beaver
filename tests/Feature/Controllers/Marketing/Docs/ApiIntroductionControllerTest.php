<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the api introduction page', function () {
    $response = $this->get('/docs/1.x/api/introduction');

    $response->assertOk();
});
