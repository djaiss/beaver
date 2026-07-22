<?php

declare(strict_types=1);

use App\Actions\CreateSupportTeamMessage;
use App\Enums\EmailType;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('adds a team reply and marks the conversation answered', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ross = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $ross->id]);

    $message = new CreateSupportTeamMessage(
        user: $admin,
        ticket: $ticket,
        body: 'We are looking into it, Ross.',
    )->execute();

    expect($message)->toBeInstanceOf(SupportMessage::class);
    expect($message->is_from_team)->toBeTrue();
    expect($message->user_id)->toBe($admin->id);
    expect($message->body)->toBe('We are looking into it, Ross.');
    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Answered);
});

it('emails the person who opened the conversation', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ross = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $ross->id]);

    new CreateSupportTeamMessage(
        user: $admin,
        ticket: $ticket,
        body: 'Fixed on our end.',
    )->execute();

    Queue::assertPushedOn(
        queue: 'high',
        job: SendEmail::class,
        callback: fn (SendEmail $job): bool => $job->user->id === $ross->id
            && $job->emailType === EmailType::SupportTeamReply,
    );

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportMessageCreation,
    );
});

it('brings a closed conversation back to answered', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->closed()->create();

    new CreateSupportTeamMessage(
        user: $admin,
        ticket: $ticket,
        body: 'Reopening to follow up.',
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Answered);
    expect($fresh->closed_by)->toBeNull();
    expect($fresh->closed_at)->toBeNull();
});

it('strips html from the team reply', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->create();

    $message = new CreateSupportTeamMessage(
        user: $admin,
        ticket: $ticket,
        body: '<b>Bold</b> answer.',
    )->execute();

    expect($message->body)->not->toContain('<b>');
    expect($message->body)->toContain('answer.');
});

it('refuses a user who does not administer the instance', function () {
    Queue::fake();

    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ticket = SupportTicket::factory()->create();

    expect(fn () => new CreateSupportTeamMessage(
        user: $rachel,
        ticket: $ticket,
        body: 'Sneaky staff reply.',
    )->execute())->toThrow(ModelNotFoundException::class);

    Queue::assertNothingPushed();
});
