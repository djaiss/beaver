<?php

declare(strict_types=1);

use App\Actions\UpdateSupportTicketAsInstanceAdmin;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('closes a conversation as the team', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->create();

    new UpdateSupportTicketAsInstanceAdmin(
        user: $admin,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Closed);
    expect($fresh->closed_by)->toBe(SupportTicketCloser::Team);
    expect($fresh->closed_at)->not->toBeNull();

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: fn (LogUserAction $job): bool => $job->action === UserActionEnum::SupportTicketUpdate,
    );
});

it('reopens a closed conversation and wipes the closure', function () {
    Queue::fake();

    $admin = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->closed()->create();

    new UpdateSupportTicketAsInstanceAdmin(
        user: $admin,
        ticket: $ticket,
        status: SupportTicketStatus::Open,
    )->execute();

    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Open);
    expect($fresh->closed_by)->toBeNull();
    expect($fresh->closed_at)->toBeNull();
});

it('refuses a user who does not administer the instance', function () {
    Queue::fake();

    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ticket = SupportTicket::factory()->create();

    expect(fn () => new UpdateSupportTicketAsInstanceAdmin(
        user: $rachel,
        ticket: $ticket,
        status: SupportTicketStatus::Closed,
    )->execute())->toThrow(ModelNotFoundException::class);

    Queue::assertNothingPushed();
});
