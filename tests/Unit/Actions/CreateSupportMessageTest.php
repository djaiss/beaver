<?php

declare(strict_types=1);
use App\Actions\CreateSupportMessage;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('adds a reply to a conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $message = new CreateSupportMessage(
        user: $user,
        ticket: $ticket,
        body: 'Thanks, I tried again and it worked!',
    )->execute();

    expect($message)->toBeInstanceOf(SupportMessage::class);
    expect($message->support_ticket_id)->toBe($ticket->id);
    expect($message->user_id)->toBe($user->id);
    expect($message->body)->toBe('Thanks, I tried again and it worked!');

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportMessageCreation,
    );
});

it('reopens a closed conversation when replying', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->closed()->create(['user_id' => $user->id]);

    new CreateSupportMessage(
        user: $user,
        ticket: $ticket,
        body: 'One more thing…',
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Open);
    expect($fresh->closed_by)->toBeNull();
    expect($fresh->closed_at)->toBeNull();
});

it('moves an answered conversation back to open when the user replies', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->answered()->create(['user_id' => $user->id]);

    new CreateSupportMessage(
        user: $user,
        ticket: $ticket,
        body: 'Thanks, but one more question…',
    )->execute();

    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Open);
});

it('strips html from the reply', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $message = new CreateSupportMessage(
        user: $user,
        ticket: $ticket,
        body: '<b>Bold</b> follow up.',
    )->execute();

    expect($message->body)->not->toContain('<b>');
    expect($message->body)->toContain('follow up.');
});

it('refuses to reply to another users conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create();

    expect(fn () => new CreateSupportMessage(
        user: $user,
        ticket: $ticket,
        body: 'Sneaky reply.',
    )->execute())->toThrow(ModelNotFoundException::class);

    Queue::assertNothingPushed();
});
