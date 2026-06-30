<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\SendWebhook;
use App\Enums\WebhookEventEnum;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Spatie\WebhookServer\CallWebhookJob;
use Tests\TestCase;

class SendWebhookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_dispatches_a_call_to_each_active_endpoint(): void
    {
        Queue::fake();

        $user = $this->createUser();
        WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://central-perk.test/webhooks',
        ]);
        WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
            'url' => 'https://moondance-diner.test/webhooks',
        ]);

        new SendWebhook(
            user: $user,
            event: WebhookEventEnum::VaultCreated,
            data: ['id' => 1, 'name' => 'Central Perk'],
        )->execute();

        Queue::assertPushed(CallWebhookJob::class, 2);

        Queue::assertPushed(
            CallWebhookJob::class,
            fn (CallWebhookJob $job): bool => (
                $job->webhookUrl === 'https://central-perk.test/webhooks'
                && $job->queue === 'low'
                && $job->payload['event'] === 'vault.created'
                && $job->payload['data'] === ['id' => 1, 'name' => 'Central Perk']
            ),
        );
    }

    #[Test]
    public function it_does_not_dispatch_to_inactive_endpoints(): void
    {
        Queue::fake();

        $user = $this->createUser();
        WebhookEndpoint::factory()->inactive()->create([
            'user_id' => $user->id,
        ]);

        new SendWebhook(
            user: $user,
            event: WebhookEventEnum::VaultCreated,
            data: ['id' => 1, 'name' => 'Central Perk'],
        )->execute();

        Queue::assertNotPushed(CallWebhookJob::class);
    }

    #[Test]
    public function it_does_not_dispatch_when_the_user_has_no_endpoints(): void
    {
        Queue::fake();

        $user = $this->createUser();

        new SendWebhook(
            user: $user,
            event: WebhookEventEnum::VaultDestroyed,
            data: ['id' => 1, 'name' => 'Central Perk'],
        )->execute();

        Queue::assertNotPushed(CallWebhookJob::class);
    }
}
