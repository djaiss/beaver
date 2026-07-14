<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the organizations page', function () {
    $response = $this->get('/docs/1.x/organizations/index');
    $response->assertOk();
});
