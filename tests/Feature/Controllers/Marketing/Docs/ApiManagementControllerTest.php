<?php

declare(strict_types=1);

it('renders the api management page', function () {
    $response = $this->get('/docs/1.x/api/account/api-management');

    $response->assertOk();
});

it('returns the api management document as markdown', function () {
    $response = $this->get('/docs/1.x/api/account/api-management.md');

    $response->assertOk();
});
