<?php

declare(strict_types=1);
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lists the users webhook endpoints', function () {
    $user = $this->createUser();
    $endpoint = WebhookEndpoint::factory()->create([
        'user_id' => $user->id,
        'url' => 'https://central-perk.test/webhooks',
    ]);

    $response = $this->actingAs($user)->get('/profile/webhooks');

    $response->assertOk();
    $response->assertSee('https://central-perk.test/webhooks');
    $response->assertSee($endpoint->secret);
});

it('does not list another users endpoints', function () {
    $user = $this->createUser();
    $otherEndpoint = WebhookEndpoint::factory()->create([
        'url' => 'https://gunther.test/webhooks',
    ]);

    $response = $this->actingAs($user)->get('/profile/webhooks');

    $response->assertOk();
    $response->assertDontSee('https://gunther.test/webhooks');
});

it('shows the create form', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/profile/webhooks/new');

    $response->assertOk();
    $response->assertSee('Endpoint URL');
});

it('creates a webhook endpoint', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/profile/webhooks/new')
        ->post('/profile/webhooks', [
            'url' => 'https://central-perk.test/webhooks',
            'label' => 'Central Perk',
        ]);

    $response->assertRedirect('/profile/webhooks');
    $response->assertSessionHas('status', 'Webhook endpoint created');

    $this->assertDatabaseCount('webhook_endpoints', 1);
    $endpoint = WebhookEndpoint::query()->first();
    expect($endpoint->user_id)->toBe($user->id);
    expect($endpoint->url)->toBe('https://central-perk.test/webhooks');
    expect($endpoint->label)->toBe('Central Perk');
    expect($endpoint->secret)->not->toBeEmpty();
});

it('validates the url when creating', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/profile/webhooks/new')
        ->post('/profile/webhooks', [
            'url' => 'not-a-valid-url',
        ]);

    $response->assertSessionHasErrors('url');
    $this->assertDatabaseCount('webhook_endpoints', 0);
});

it('deletes a webhook endpoint', function () {
    Queue::fake();

    $user = $this->createUser();
    $endpoint = WebhookEndpoint::factory()->create([
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->delete('/profile/webhooks/'.$endpoint->id);

    $response->assertRedirect('/profile/webhooks');
    $response->assertSessionHas('status', 'Webhook endpoint deleted');
    $this->assertModelMissing($endpoint);
});

it('cannot delete another users endpoint', function () {
    $user = $this->createUser();
    $otherEndpoint = WebhookEndpoint::factory()->create();

    $response = $this->actingAs($user)
        ->delete('/profile/webhooks/'.$otherEndpoint->id);

    $response->assertNotFound();
    $this->assertModelExists($otherEndpoint);
});
