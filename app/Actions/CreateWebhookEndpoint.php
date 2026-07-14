<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\User;
use App\Models\WebhookEndpoint;
use Illuminate\Support\Str;

/**
 * Register a webhook endpoint for a user.
 * The signing secret is generated here so the user never has to provide one.
 */
class CreateWebhookEndpoint
{
    private WebhookEndpoint $endpoint;

    public function __construct(
        private readonly User $user,
        private readonly string $url,
        private ?string $label = null,
    ) {}

    public function execute(): WebhookEndpoint
    {
        $this->sanitize();
        $this->create();
        $this->log();

        return $this->endpoint;
    }

    private function sanitize(): void
    {
        if ($this->label === null) {
            return;
        }

        $this->label = TextSanitizer::plainText($this->label);

        if ($this->label === '') {
            $this->label = null;
        }
    }

    private function create(): void
    {
        $this->endpoint = WebhookEndpoint::query()->create([
            'user_id' => $this->user->id,
            'label' => $this->label,
            'url' => $this->url,
            'secret' => Str::random(64),
            'is_active' => true,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::WebhookEndpointCreation,
        )->onQueue('low');
    }
}
