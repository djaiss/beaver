<?php

declare(strict_types=1);
use App\Models\EmailSent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;

uses(RefreshDatabase::class);

it('shows all the emails', function () {
    Date::setTestNow(Date::create(2025, 1, 1));
    $user = $this->createUser();

    EmailSent::factory()->create([
        'user_id' => $user->id,
        'email_address' => 'test@example.com',
        'subject' => 'Test Email',
        'sent_at' => now(),
    ]);

    $response = $this->actingAs($user)
        ->get('/settings/emails');

    $response->assertStatus(200);
    $response->assertViewIs('app.settings.emails.index');
    $response->assertViewHas('emails');
});
