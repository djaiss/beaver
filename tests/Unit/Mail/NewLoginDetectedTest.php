<?php

declare(strict_types=1);
use App\Mail\NewLoginDetected;

it('should have correct envelope subject', function () {
    $mailable = new NewLoginDetected(
        device: 'Rachel iPhone 15',
    );

    expect($mailable->envelope()->subject)->toEqual('New sign-in to your account');

    $rendered = $mailable->render();

    $this->assertStringContainsString('Rachel iPhone 15', $rendered);
});
