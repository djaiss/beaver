<?php

declare(strict_types=1);
use App\Mail\LoginFailed;
use Illuminate\Support\Facades\Config;

it('should have correct envelope subject', function () {
    Config::set('app.name', 'kollek');

    $mailable = new LoginFailed;

    expect($mailable->envelope()->subject)->toEqual('Login attempt on kollek');

    $rendered = $mailable->render();

    $this->assertStringContainsString('Login attempt on kollek', $rendered);
});
