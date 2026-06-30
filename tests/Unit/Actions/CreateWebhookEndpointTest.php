<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateWebhookEndpoint;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateWebhookEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_webhook_endpoint_with_a_generated_secret(): void
    {
        Queue::fake();

        $user = $this->createUser();

        $endpoint = new CreateWebhookEndpoint(
            user: $user,
            url: 'https://central-perk.test/webhooks',
            label: 'Central Perk',
        )->execute();

        $this->assertInstanceOf(WebhookEndpoint::class, $endpoint);

        $this->assertDatabaseHas('webhook_endpoints', [
            'id' => $endpoint->id,
            'user_id' => $user->id,
        ]);

        $this->assertSame('https://central-perk.test/webhooks', $endpoint->url);
        $this->assertSame('Central Perk', $endpoint->label);
        $this->assertTrue($endpoint->is_active);
        $this->assertNotEmpty($endpoint->secret);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::WebhookEndpointCreation
                && $job->user->id === $user->id
            ),
        );
    }

    #[Test]
    public function it_stores_a_null_label_when_none_is_given(): void
    {
        Queue::fake();

        $user = $this->createUser();

        $endpoint = new CreateWebhookEndpoint(
            user: $user,
            url: 'https://central-perk.test/webhooks',
        )->execute();

        $this->assertNull($endpoint->label);
    }
}
