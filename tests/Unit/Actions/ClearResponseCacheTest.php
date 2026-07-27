<?php

declare(strict_types=1);

use App\Actions\ClearResponseCache;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Spatie\ResponseCache\Facades\ResponseCache;

uses(RefreshDatabase::class);

it('clears the response cache', function () {
    Queue::fake();
    ResponseCache::spy();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    new ClearResponseCache(user: $monica)->execute();

    ResponseCache::shouldHaveReceived('clear');
});

it('logs the clearing', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);

    new ClearResponseCache(user: $monica)->execute();

    Queue::assertPushed(LogUserAction::class, fn (LogUserAction $job): bool => $job->action === UserActionEnum::ResponseCacheCleared);
});

it('forbids a user who does not administer the instance', function () {
    Queue::fake();
    ResponseCache::spy();
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    expect(fn () => new ClearResponseCache(user: $rachel)->execute())
        ->toThrow(ModelNotFoundException::class);

    ResponseCache::shouldNotHaveReceived('clear');
});
