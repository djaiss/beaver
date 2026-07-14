<?php

declare(strict_types=1);
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

it('renders the reset password screen', function () {
    $response = $this->get('/reset-password/fake-token');

    $response->assertStatus(200);
    $response->assertViewIs('app.auth.reset-password');
});
it('resets password with valid token', function () {
    Event::fake();

    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $token = Password::createToken($user);

    $response = $this->post('/reset-password', [
        'token' => $token,
        'email' => 'chandler.bing@friends.com',
        'password' => 'SecureP@ssw0rd!2024',
        'password_confirmation' => 'SecureP@ssw0rd!2024',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHas('status');

    expect(Hash::check('SecureP@ssw0rd!2024', $user->fresh()->password))->toBeTrue();
    Event::assertDispatched(PasswordReset::class);
});
it('rejects invalid token', function () {
    $user = User::factory()->create([
        'email' => 'chandler.bing@friends.com',
    ]);

    $response = $this->post('/reset-password', [
        'token' => 'invalid-token',
        'email' => 'chandler.bing@friends.com',
        'password' => 'SecureP@ssw0rd!2024',
        'password_confirmation' => 'SecureP@ssw0rd!2024',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasErrors(['email']);

    expect(Hash::check('SecureP@ssw0rd!2024', $user->fresh()->password))->toBeFalse();
});
