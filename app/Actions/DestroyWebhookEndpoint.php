<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use App\Models\WebhookEndpoint;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyWebhookEndpoint
{
    public function __construct(
        private readonly User $user,
        private readonly WebhookEndpoint $webhookEndpoint,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->webhookEndpoint->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->webhookEndpoint->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Webhook endpoint not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::WebhookEndpointDeletion,
        )->onQueue('low');
    }
}
