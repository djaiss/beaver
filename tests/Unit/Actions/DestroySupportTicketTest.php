<?php

declare(strict_types=1);
use App\Actions\DestroySupportTicket;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('deletes a conversation and its messages', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);
    $message = SupportMessage::factory()->create([
        'support_ticket_id' => $ticket->id,
        'user_id' => $user->id,
    ]);

    new DestroySupportTicket(
        user: $user,
        ticket: $ticket,
    )->execute();

    $this->assertModelMissing($ticket);
    $this->assertModelMissing($message);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportTicketDeletion,
    );
});

it('refuses to delete another users conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create();

    expect(fn () => new DestroySupportTicket(
        user: $user,
        ticket: $ticket,
    )->execute())->toThrow(ModelNotFoundException::class);

    $this->assertModelExists($ticket);
    Queue::assertNothingPushed();
});
