<?php

declare(strict_types=1);

use App\Actions\PurgeCloudflareCache;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('services.cloudflare.api_token', 'token-of-the-one-with-the-cache');
    config()->set('services.cloudflare.zone_id', 'zone-central-perk');
});

it('purges the cloudflare cache', function () {
    Queue::fake();
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => true])]);
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $purged = new PurgeCloudflareCache(user: $monica)->execute();

    expect($purged)->toBeTrue();
    Http::assertSent(fn ($request): bool => $request->url() === 'https://api.cloudflare.com/client/v4/zones/zone-central-perk/purge_cache');
});

it('reports a purge cloudflare refused', function () {
    Queue::fake();
    Http::fake(['api.cloudflare.com/*' => Http::response(['success' => false], 403)]);
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $purged = new PurgeCloudflareCache(user: $monica)->execute();

    expect($purged)->toBeFalse();
});

it('logs the purge', function () {
    Queue::fake();
    Http::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    new PurgeCloudflareCache(user: $monica)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::CloudflareCachePurged);
});

it('forbids a user who does not administer the instance', function () {
    Queue::fake();
    Http::fake();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    expect(fn () => new PurgeCloudflareCache(user: $rachel)->execute())
        ->toThrow(ModelNotFoundException::class);

    Http::assertNothingSent();
});
