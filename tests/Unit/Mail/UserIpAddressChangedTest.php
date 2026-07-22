<?php

declare(strict_types=1);
use App\Mail\UserIpAddressChanged;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;

uses(RefreshDatabase::class);

it('should have correct envelope subject', function () {
    Config::set('app.name', 'kollek');

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $mailable = new UserIpAddressChanged(
        user: $user,
        ip: '192.168.1.1',
    );

    expect($mailable->envelope()->subject)->toEqual('New sign-in detected on your kollek account');

    $rendered = $mailable->render();

    $this->assertStringContainsString('chandler.bing@friends.com', $rendered);
    $this->assertStringContainsString('192.168.1.1', $rendered);
});
