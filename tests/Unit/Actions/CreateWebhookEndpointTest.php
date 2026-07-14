<?php

declare(strict_types=1);
use App\Actions\CreateWebhookEndpoint;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('creates a webhook endpoint with a generated secret', function () {
    Queue::fake();

    $user = $this->createUser();

    $endpoint = new CreateWebhookEndpoint(
        user: $user,
        url: 'https://central-perk.test/webhooks',
        label: 'Central Perk',
    )->execute();

    expect($endpoint)->toBeInstanceOf(WebhookEndpoint::class);

    $this->assertDatabaseHas('webhook_endpoints', [
        'id' => $endpoint->id,
        'user_id' => $user->id,
    ]);

    expect($endpoint->url)->toBe('https://central-perk.test/webhooks');
    expect($endpoint->label)->toBe('Central Perk');
    expect($endpoint->is_active)->toBeTrue();
    expect($endpoint->secret)->not->toBeEmpty();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::WebhookEndpointCreation
            && $job->user->id === $user->id
        ),
    );
});

it('stores a null label when none is given', function () {
    Queue::fake();

    $user = $this->createUser();

    $endpoint = new CreateWebhookEndpoint(
        user: $user,
        url: 'https://central-perk.test/webhooks',
    )->execute();

    expect($endpoint->label)->toBeNull();
});
