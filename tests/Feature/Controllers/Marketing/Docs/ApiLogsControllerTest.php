<?php

declare(strict_types=1);

it('renders the api logs page', function () {
    $response = $this->get('/docs/1.x/api/account/logs');

    $response->assertOk();
});

it('returns the logs document as markdown', function () {
    $response = $this->get('/docs/1.x/api/account/logs.md');

    $response->assertOk();
});
