<?php

declare(strict_types=1);
use App\Mail\ApiKeyCreated;

it('should have correct envelope subject', function () {
    $mailable = new ApiKeyCreated(
        label: 'Production API Key',
    );

    expect($mailable->envelope()->subject)->toEqual('New API key added');

    $rendered = $mailable->render();

    $this->assertStringContainsString('Production API Key', $rendered);
});
