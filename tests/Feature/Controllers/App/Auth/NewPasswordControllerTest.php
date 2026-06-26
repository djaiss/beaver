<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_reset_password_screen(): void
    {
        $response = $this->get('/reset-password/fake-token');

        $response->assertOk();
        $response->assertViewIs('app.auth.reset-password');
    }

    #[Test]
    public function it_resets_password_with_valid_token(): void
    {
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

        $this->assertTrue(Hash::check('SecureP@ssw0rd!2024', $user->fresh()->password));
        Event::assertDispatched(PasswordReset::class);
    }

    #[Test]
    public function it_rejects_invalid_token(): void
    {
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

        $this->assertFalse(Hash::check('SecureP@ssw0rd!2024', $user->fresh()->password));
    }
}
