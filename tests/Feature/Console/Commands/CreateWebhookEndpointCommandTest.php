<?php

declare(strict_types=1);
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a webhook endpoint for a user', function () {
    $user = $this->createUser(['email' => 'rachel@central-perk.test']);

    $this->artisan('beaver:create-webhook-endpoint', [
        'email' => 'rachel@central-perk.test',
        'url' => 'https://rachel.test/webhooks',
        '--label' => 'Rachel',
    ])->assertSuccessful();

    $endpoint = WebhookEndpoint::query()->where('user_id', $user->id)->first();

    expect($endpoint)->not->toBeNull();
    expect($endpoint->url)->toBe('https://rachel.test/webhooks');
    expect($endpoint->label)->toBe('Rachel');
    expect($endpoint->is_active)->toBeTrue();
});
it('fails when no user matches the email', function () {
    $this->artisan('beaver:create-webhook-endpoint', [
        'email' => 'gunther@central-perk.test',
        'url' => 'https://gunther.test/webhooks',
    ])->assertFailed();

    $this->assertDatabaseCount('webhook_endpoints', 0);
});
