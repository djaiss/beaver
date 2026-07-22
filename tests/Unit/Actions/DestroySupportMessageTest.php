<?php

declare(strict_types=1);
use App\Actions\DestroySupportMessage;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a message', function () {
    Queue::fake();

    $user = $this->createUser();
    $message = SupportMessage::factory()->create(['user_id' => $user->id]);

    new DestroySupportMessage(
        user: $user,
        message: $message,
    )->execute();

    $this->assertModelMissing($message);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportMessageDeletion,
    );
});

it('refuses to delete another users message', function () {
    Queue::fake();

    $user = $this->createUser();
    $message = SupportMessage::factory()->create();

    expect(fn () => new DestroySupportMessage(
        user: $user,
        message: $message,
    )->execute())->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($message);
    Queue::assertNothingPushed();
});
