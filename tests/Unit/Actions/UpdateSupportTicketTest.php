<?php

declare(strict_types=1);
use App\Actions\UpdateSupportTicket;
use App\Enums\SupportCategory;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('closes a conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'status' => SupportTicketStatus::Open,
    ]);

    new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Closed);
    expect($fresh->closed_by)->toBe(SupportTicketCloser::User);
    expect($fresh->closed_at)->not->toBeNull();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportTicketUpdate,
    );
});

it('records the support team as the closer when they close it', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'status' => SupportTicketStatus::Open,
    ]);

    new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
        closedBy: SupportTicketCloser::Team,
    )->execute();

    expect($ticket->fresh()->closed_by)->toBe(SupportTicketCloser::Team);
});

it('clears the closure when reopening', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->closed()->create(['user_id' => $user->id]);

    new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        status: SupportTicketStatus::Open,
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Open);
    expect($fresh->closed_by)->toBeNull();
    expect($fresh->closed_at)->toBeNull();
});

it('updates the category', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'category' => SupportCategory::HowTo,
    ]);

    new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        category: SupportCategory::BugReport,
    )->execute();

    expect($ticket->fresh()->category)->toBe(SupportCategory::BugReport);
});

it('leaves untouched fields alone', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'category' => SupportCategory::Billing,
        'status' => SupportTicketStatus::Open,
    ]);

    new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
    )->execute();

    expect($ticket->fresh()->category)->toBe(SupportCategory::Billing);
});

it('refuses to update another users conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create();

    expect(fn () => new UpdateSupportTicket(
        user: $user,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
    )->execute())->toThrow(ModelNotFoundException::class);

    Queue::assertNothingPushed();
});
