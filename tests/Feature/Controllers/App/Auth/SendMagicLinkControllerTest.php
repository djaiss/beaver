<?php

declare(strict_types=1);
use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('renders the request magic link screen', function () {
    $response = $this->get('/send-magic-link');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.request-magic-link');
});

it('sends a magic link for existing user', function () {
    Queue::fake();

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/send-magic-link', [
        'email' => 'chandler.bing@friends.com',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.magic-link-sent');

    Queue::assertPushed(
        SendEmail::class,
        fn (SendEmail $job): bool => (
            $job->emailType === EmailType::MagicLinkCreated
            && $job->user->id === $user->id
        ),
    );
});

it('does not reveal if user does not exist', function () {
    Queue::fake();

    $response = $this->post('/send-magic-link', [
        'email' => 'nonexistent@example.com',
    ]);

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.magic-link-sent');

    Queue::assertNotPushed(SendEmail::class);
});
