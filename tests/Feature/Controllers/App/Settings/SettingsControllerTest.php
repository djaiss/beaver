<?php

declare(strict_types=1);
use App\Models\EmailSent;
use App\Models\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the settings page', function () {
    $user = $this->createUser();

    Log::factory()->create([
        'user_id' => $user->id,
    ]);
    EmailSent::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->get('/profile');

    $response
        ->assertOk()
        ->assertViewHasAll([
            'user',
            'logs',
            'hasMoreLogs',
            'emails',
            'hasMoreEmails',
        ]);

    $response->assertViewHas(
        'logs',
        fn ($logs): bool => $logs->count() === 1
        && $logs->every(
            fn ($log): bool => isset(
                $log->username,
                $log->action,
                $log->description,
                $log->created_at,
                $log->created_at_human,
            ),
        ),
    );

    $response->assertViewHas(
        'emails',
        fn ($emails): bool => $emails->count() === 1
        || $emails->every(
            fn ($email): bool => isset(
                $email->email_address,
                $email->subject,
                $email->body,
                $email->sent_at,
                $email->delivered_at,
                $email->bounced_at,
            ),
        ),
    );
});

it('renders the section title help popovers on the profile page', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/profile');

    $response->assertOk();
    $response->assertSee('picture that represents you across the app');
    $response->assertSee('Your name, nickname, email');
    $response->assertSee('personal history of everything you have done');
    $response->assertSee('Every email KolleK has sent you');
});

it('updates the profile information', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->put('/profile', [
            'first_name' => 'Chandler',
            'last_name' => 'Bing',
            'nickname' => 'Chan',
            'email' => 'chandler.bing@friends.com',
            'locale' => 'de_DE',
            'time_format_24h' => 'true',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');
});
