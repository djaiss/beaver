<?php

declare(strict_types=1);
use App\Actions\CreateSupportTicket;
use App\Enums\SupportCategory;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('opens a conversation with a subject and a first message', function () {
    Queue::fake();

    $user = $this->createUser();

    $ticket = new CreateSupportTicket(
        user: $user,
        category: SupportCategory::BugReport,
        subject: 'Import keeps timing out',
        message: 'The import keeps timing out on my collection.',
    )->execute();

    expect($ticket)->toBeInstanceOf(SupportTicket::class);
    expect($ticket->user_id)->toBe($user->id);
    expect($ticket->subject)->toBe('Import keeps timing out');
    expect($ticket->category)->toBe(SupportCategory::BugReport);
    expect($ticket->status)->toBe(SupportTicketStatus::Open);

    expect($ticket->messages()->count())->toBe(1);
    $message = $ticket->messages()->first();
    expect($message->user_id)->toBe($user->id);
    expect($message->body)->toBe('The import keeps timing out on my collection.');
});

it('strips html from the subject and the message', function () {
    Queue::fake();

    $user = $this->createUser();

    $ticket = new CreateSupportTicket(
        user: $user,
        category: SupportCategory::Billing,
        subject: '<b>Invoice</b> question',
        message: '<script>alert("hi")</script>Please check my invoice.',
    )->execute();

    expect($ticket->subject)->not->toContain('<b>');
    expect($ticket->subject)->toContain('Invoice');
    expect($ticket->messages()->first()->body)->not->toContain('<script>');
    expect($ticket->messages()->first()->body)->toContain('Please check my invoice.');
});

it('logs the creation on the low queue', function () {
    Queue::fake();

    $user = $this->createUser();

    new CreateSupportTicket(
        user: $user,
        category: SupportCategory::Billing,
        subject: 'A billing question',
        message: 'A billing question.',
    )->execute();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => (
            $job->action === UserActionEnum::SupportTicketCreation
            && $job->user->id === $user->id
        ),
    );
});
