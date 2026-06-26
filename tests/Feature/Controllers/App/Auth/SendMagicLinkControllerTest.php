<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendMagicLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_request_magic_link_screen(): void
    {
        $response = $this->get('/send-magic-link');

        $response->assertOk();
        $response->assertViewIs('app.auth.request-magic-link');
    }

    #[Test]
    public function it_sends_a_magic_link_for_existing_user(): void
    {
        Queue::fake();

        $user = User::factory()->create([
            'email' => 'chandler.bing@friends.com',
        ]);

        $response = $this->post('/send-magic-link', [
            'email' => 'chandler.bing@friends.com',
        ]);

        $response->assertOk();
        $response->assertViewIs('app.auth.magic-link-sent');

        Queue::assertPushed(
            SendEmail::class,
            fn (SendEmail $job): bool => (
                $job->emailType === EmailType::MagicLinkCreated
                && $job->user->id === $user->id
            ),
        );
    }

    #[Test]
    public function it_does_not_reveal_if_user_does_not_exist(): void
    {
        Queue::fake();

        $response = $this->post('/send-magic-link', [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertOk();
        $response->assertViewIs('app.auth.magic-link-sent');

        Queue::assertNotPushed(SendEmail::class);
    }
}
