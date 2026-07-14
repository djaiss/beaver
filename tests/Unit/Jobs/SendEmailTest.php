<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Mail\LoginFailed;
use App\Models\EmailSent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Resend\Email;

uses(RefreshDatabase::class);

it('sends email the traditional way', function () {
    Config::set('app.use_resend', false);
    Config::set('app.name', 'beaver');
    Config::set('mail.from.address', 'noreply@example.com');
    Mail::fake();

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $job = new SendEmail(
        mailable: new LoginFailed,
        user: $user,
        emailType: EmailType::LoginFailed,
    );

    $job->handle();

    Mail::assertQueued(
        LoginFailed::class,
        fn (LoginFailed $mail) => $mail->hasTo($user->email),
    );

    $emailSent = EmailSent::query()->latest()->first();
    expect($emailSent->email_type)->toEqual(EmailType::LoginFailed->value);
    expect($emailSent->email_address)->toEqual('chandler.bing@friends.com');
    expect($emailSent->subject)->toEqual('Login attempt on beaver');
});
it('sends email with resend facade', function () {
    Config::set('app.use_resend', true);
    Config::set('app.name', 'beaver');
    Config::set('mail.from.address', 'noreply@example.com');

    $resendMock = Mockery::mock();
    $emailsMock = Mockery::mock(Resend\Service\Email::class);

    $emailsMock
        ->shouldReceive('send')
        ->once()
        ->with(Mockery::on(
            fn ($args): bool => (
                $args['from'] === 'noreply@example.com'
                && $args['to'] === ['chandler.bing@friends.com']
                && $args['subject'] === 'Login attempt on beaver'
                && is_string($args['html'])
                && mb_strlen($args['html']) > 0
            ),
        ))
        ->andReturn(Email::from(['id' => 'resend-uuid-12345']));

    // Mock the emails() method to return the emails mock
    $resendMock
        ->shouldReceive('emails')
        ->once()
        ->andReturn($emailsMock);

    // Replace the Resend service binding with our mock
    app()->instance('resend', $resendMock);

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $job = new SendEmail(
        mailable: new LoginFailed,
        user: $user,
        emailType: EmailType::LoginFailed,
    );

    $job->handle();

    $emailSent = EmailSent::query()->latest()->first();
    expect($emailSent->email_type)->toEqual(EmailType::LoginFailed->value);
    expect($emailSent->email_address)->toEqual('chandler.bing@friends.com');
    expect($emailSent->subject)->toEqual('Login attempt on beaver');
    expect($emailSent->uuid)->toEqual('resend-uuid-12345');
});
