<?php

declare(strict_types=1);
use App\Actions\CreateMagicLink;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('returns a string', function () {
    Queue::fake();

    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $magicLinkUrl = new CreateMagicLink(
        email: $user->email,
    )->execute();

    expect($magicLinkUrl)->toBeString();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::MagicLinkCreated
            && $job->user->id === $user->id
        ),
    );
});

it('contains the app url with magic link structure', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $magicLinkUrl = new CreateMagicLink(
        email: $user->email,
    )->execute();

    $appUrl = config('app.url');
    expect($magicLinkUrl)->toStartWith($appUrl.'/magiclink/');
    expect($magicLinkUrl)->toMatch('/\/magiclink\/[a-f0-9-]+%3A[A-Za-z0-9]+/');
});

it('throws an exception if user not found', function () {
    $nonExistentEmail = 'nonexistent@example.com';

    $this->expectException(ModelNotFoundException::class);

    new CreateMagicLink(
        email: $nonExistentEmail,
    )->execute();
});
