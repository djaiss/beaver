<?php

declare(strict_types=1);
use App\Mail\MagicLinkCreated;
use Illuminate\Support\Facades\Config;

it('should have correct envelope subject', function () {
    Config::set('app.name', 'kollek');

    $mailable = new MagicLinkCreated(
        link: 'https://example.com/magic-link/abc123',
    );

    expect($mailable->envelope()->subject)->toEqual('Login to kollek');

    $rendered = $mailable->render();

    $this->assertStringContainsString('https://example.com/magic-link/abc123', $rendered);
});
