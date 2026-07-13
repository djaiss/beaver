<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateWebhookEndpointCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_webhook_endpoint_for_a_user(): void
    {
        $user = $this->createUser(['email' => 'rachel@central-perk.test']);

        $this->artisan('beaver:create-webhook-endpoint', [
            'email' => 'rachel@central-perk.test',
            'url' => 'https://rachel.test/webhooks',
            '--label' => 'Rachel',
        ])->assertSuccessful();

        $endpoint = WebhookEndpoint::query()->where('user_id', $user->id)->first();

        $this->assertNotNull($endpoint);
        $this->assertSame('https://rachel.test/webhooks', $endpoint->url);
        $this->assertSame('Rachel', $endpoint->label);
        $this->assertTrue($endpoint->is_active);
    }

    #[Test]
    public function it_fails_when_no_user_matches_the_email(): void
    {
        $this->artisan('beaver:create-webhook-endpoint', [
            'email' => 'gunther@central-perk.test',
            'url' => 'https://gunther.test/webhooks',
        ])->assertFailed();

        $this->assertDatabaseCount('webhook_endpoints', 0);
    }
}
