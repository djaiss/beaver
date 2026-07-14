<?php

declare(strict_types=1);
use App\Mail\UserAutomaticallyDeleted;

it('should have correct envelope subject', function () {
    $mailable = new UserAutomaticallyDeleted(
        age: '90 days',
    );

    expect($mailable->envelope()->subject)->toEqual('Account automatically deleted');

    $rendered = $mailable->render();

    $this->assertStringContainsString('Account deleted', $rendered);
    $this->assertStringContainsString('automatically deleted because of inactivity', $rendered);
    $this->assertStringContainsString('90 days', $rendered);
});
