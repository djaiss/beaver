<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_user(): void
    {
        $user = $this->createUser();
        $endpoint = WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($endpoint->user()->exists());
    }

    #[Test]
    public function it_encrypts_the_url_and_secret(): void
    {
        $endpoint = WebhookEndpoint::factory()->create([
            'url' => 'https://chandler.test/webhooks',
            'secret' => 'could-i-be-any-more-secret',
        ]);

        $this->assertSame('https://chandler.test/webhooks', $endpoint->url);
        $this->assertSame('could-i-be-any-more-secret', $endpoint->secret);

        $raw = DB::table('webhook_endpoints')->where('id', $endpoint->id)->value('url');
        $this->assertNotSame('https://chandler.test/webhooks', $raw);
        $this->assertSame('https://chandler.test/webhooks', decrypt($raw, false));
    }
}
