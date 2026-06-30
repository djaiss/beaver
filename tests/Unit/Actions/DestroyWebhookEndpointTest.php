<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyWebhookEndpoint;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\WebhookEndpoint;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyWebhookEndpointTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_webhook_endpoint(): void
    {
        Queue::fake();

        $user = $this->createUser();
        $endpoint = WebhookEndpoint::factory()->create([
            'user_id' => $user->id,
        ]);

        new DestroyWebhookEndpoint(
            user: $user,
            webhookEndpoint: $endpoint,
        )->execute();

        $this->assertModelMissing($endpoint);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === UserActionEnum::WebhookEndpointDeletion
                && $job->user->id === $user->id
            ),
        );
    }

    #[Test]
    public function it_throws_an_exception_if_the_endpoint_does_not_belong_to_the_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = $this->createUser();
        $endpoint = WebhookEndpoint::factory()->create();

        new DestroyWebhookEndpoint(
            user: $user,
            webhookEndpoint: $endpoint,
        )->execute();
    }
}
