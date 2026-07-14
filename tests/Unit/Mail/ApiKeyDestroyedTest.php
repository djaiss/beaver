<?php

declare(strict_types=1);
use App\Mail\ApiKeyDestroyed;

it('has the correct data', function () {
    $mailable = new ApiKeyDestroyed(label: 'My Key');

    expect($mailable->envelope()->subject)->toBe('API key removed');

    $content = $mailable->content();

    expect($content->text)->toBe('mail.api.destroyed-text');
    expect($content->markdown)->toBe('mail.api.destroyed');
    expect($content->with['label'])->toBe('My Key');
});
