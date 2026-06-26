<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_forgot_password_screen(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertViewIs('app.auth.forgot-password');
    }

    #[Test]
    public function it_sends_a_password_reset_link(): void
    {
        Notification::fake();

        User::factory()->create([
            'email' => 'chandler.bing@friends.com',
        ]);

        $response = $this->post('/forgot-password', [
            'email' => 'chandler.bing@friends.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');
    }
}
