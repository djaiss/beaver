<?php

declare(strict_types=1);
use App\Actions\DestroyWebhookEndpoint;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\WebhookEndpoint;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a webhook endpoint', function () {
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
});

it('throws an exception if the endpoint does not belong to the user', function () {
    $this->expectException(ModelNotFoundException::class);

    $user = $this->createUser();
    $endpoint = WebhookEndpoint::factory()->create();

    new DestroyWebhookEndpoint(
        user: $user,
        webhookEndpoint: $endpoint,
    )->execute();
});
