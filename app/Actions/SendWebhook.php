<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\WebhookEventEnum;
use App\Models\User;
use App\Models\WebhookEndpoint;
use Spatie\WebhookServer\WebhookCall;

/**
 * Send an outgoing webhook to each of the user's active endpoints.
 * The actual HTTP call is queued, signed and retried by the webhook server.
 */
class SendWebhook
{
    public function __construct(
        private readonly User $user,
        private readonly WebhookEventEnum $event,
        private readonly array $data,
    ) {}

    public function execute(): void
    {
        $this->user->webhookEndpoints()
            ->where('is_active', true)
            ->get()
            ->each(fn (WebhookEndpoint $endpoint) => $this->send($endpoint));
    }

    private function send(WebhookEndpoint $endpoint): void
    {
        WebhookCall::create()
            ->url($endpoint->url)
            ->useSecret($endpoint->secret)
            ->onQueue('low')
            ->payload([
                'event' => $this->event->value,
                'happened_at' => now()->toIso8601String(),
                'data' => $this->data,
            ])
            ->dispatch();
    }
}
