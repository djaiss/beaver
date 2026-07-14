<?php

declare(strict_types=1);
use App\Mail\UserDeleted;

it('should have correct envelope subject', function () {
    $mailable = new UserDeleted(
        reason: 'No longer needed',
        activeSince: '2024-01-15',
    );

    expect($mailable->envelope()->subject)->toEqual('Account deleted');

    $rendered = $mailable->render();

    $this->assertStringContainsString('No longer needed', $rendered);
    $this->assertStringContainsString('2024-01-15', $rendered);
});
