<?php

declare(strict_types=1);
it('renders the api emails page', function () {
    $response = $this->get('/docs/1.x/api/account/emails');

    $response->assertOk();
});
it('returns the emails document as markdown', function () {
    $response = $this->get('/docs/1.x/api/account/emails.md');

    $response->assertOk();
});
