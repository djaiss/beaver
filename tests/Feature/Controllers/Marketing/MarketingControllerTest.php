<?php

declare(strict_types=1);
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the marketing homepage', function () {
    $response = $this->get('/');
    $response->assertOk();
});
