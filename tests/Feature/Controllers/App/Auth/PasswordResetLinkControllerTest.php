<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('renders the forgot password screen', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.forgot-password');
});
it('sends a password reset link', function () {
    Notification::fake();

    User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/forgot-password', [
        'email' => 'chandler.bing@friends.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('status');
});
