<?php

declare(strict_types=1);
use App\Models\WebhookEndpoint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $user = $this->createUser();
    $endpoint = WebhookEndpoint::factory()->create([
        'user_id' => $user->id,
    ]);

    expect($endpoint->user()->exists())->toBeTrue();
});
it('encrypts the url and secret', function () {
    $endpoint = WebhookEndpoint::factory()->create([
        'url' => 'https://chandler.test/webhooks',
        'secret' => 'could-i-be-any-more-secret',
    ]);

    expect($endpoint->url)->toBe('https://chandler.test/webhooks');
    expect($endpoint->secret)->toBe('could-i-be-any-more-secret');

    $raw = DB::table('webhook_endpoints')->where('id', $endpoint->id)->value('url');
    $this->assertNotSame('https://chandler.test/webhooks', $raw);
    expect(decrypt($raw, false))->toBe('https://chandler.test/webhooks');
});
