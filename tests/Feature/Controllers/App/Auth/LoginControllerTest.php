<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Jobs\SendEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('renders the login screen', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
});
it('authenticates a user', function () {
    config(['app.show_marketing_site' => false]);
    $user = $this->createUser();

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('accounts.index', absolute: false));
});
it('sends an email on failed login', function () {
    Queue::fake();
    config(['app.show_marketing_site' => false]);

    $user = $this->createUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => $job->emailType === EmailType::LoginFailed && $job->user->id === $user->id,
    );
});
it('does not authenticate a user with invalid password', function () {
    config(['app.show_marketing_site' => false]);
    $user = $this->createUser();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});
it('logs out a user', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    $response->assertRedirect('/');
});
