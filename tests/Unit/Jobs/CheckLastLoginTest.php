<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Jobs\CheckLastLogin;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('sends email when ip address changes', function () {
    Queue::fake();

    $user = User::factory()->create([
        'last_used_ip' => '192.168.1.1',
    ]);

    $job = new CheckLastLogin(
        user: $user,
        ip: '192.168.1.2',
    );

    $job->handle();

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => $job->user->id === $user->id && $job->emailType === EmailType::UserIpChanged,
    );

    expect($user->fresh()->last_used_ip)->toEqual('192.168.1.2');
});

it('does not send email when ip address does not change', function () {
    Queue::fake();

    $user = User::factory()->create([
        'last_used_ip' => '192.168.1.1',
    ]);

    $job = new CheckLastLogin(
        user: $user,
        ip: '192.168.1.1',
    );

    $job->handle();

    Queue::assertNotPushed(SendEmail::class);

    expect($user->fresh()->last_used_ip)->toEqual('192.168.1.1');
});

it('does not send email on first login', function () {
    Queue::fake();

    $user = User::factory()->create([
        'last_used_ip' => null,
    ]);

    $job = new CheckLastLogin(
        user: $user,
        ip: '192.168.1.1',
    );

    $job->handle();

    Queue::assertNotPushed(SendEmail::class);

    expect($user->fresh()->last_used_ip)->toEqual('192.168.1.1');
});
