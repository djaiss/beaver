<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('loads the verify email view', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.verify-email');
});
it('redirects to the dashboard if the email is verified', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/verify-email');

    $response->assertRedirect(route('dashboard.index', absolute: false));
});
it('resends a verification email', function () {
    Notification::fake();

    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->post('/verify-email');

    $response->assertRedirect();
    $response->assertSessionHas('status', 'verification-link-sent');
});
