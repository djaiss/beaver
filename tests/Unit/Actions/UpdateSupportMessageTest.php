<?php

declare(strict_types=1);
use App\Actions\UpdateSupportMessage;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('edits the body of a message', function () {
    Queue::fake();

    $user = $this->createUser();
    $message = SupportMessage::factory()->create([
        'user_id' => $user->id,
        'body' => 'Original text.',
    ]);

    new UpdateSupportMessage(
        user: $user,
        message: $message,
        body: 'Edited text.',
    )->execute();

    expect($message->fresh()->body)->toBe('Edited text.');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportMessageUpdate,
    );
});

it('strips html when editing', function () {
    Queue::fake();

    $user = $this->createUser();
    $message = SupportMessage::factory()->create(['user_id' => $user->id]);

    new UpdateSupportMessage(
        user: $user,
        message: $message,
        body: '<i>Italic</i> edit.',
    )->execute();

    expect($message->fresh()->body)->not->toContain('<i>');
});

it('refuses to edit another users message', function () {
    Queue::fake();

    $user = $this->createUser();
    $message = SupportMessage::factory()->create();

    expect(fn () => new UpdateSupportMessage(
        user: $user,
        message: $message,
        body: 'Tampered.',
    )->execute())->toThrow(ModelNotFoundException::class);

    Queue::assertNothingPushed();
});
