<?php

declare(strict_types=1);
use App\Enums\SupportCategory;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('belongs to a user', function () {
    $ticket = SupportTicket::factory()->create();

    expect($ticket->user()->exists())->toBeTrue();
    expect($ticket->user)->toBeInstanceOf(User::class);
});

it('has many messages', function () {
    $ticket = SupportTicket::factory()->create();
    SupportMessage::factory()->count(2)->create([
        'support_ticket_id' => $ticket->id,
    ]);

    expect($ticket->messages()->count())->toBe(2);
    expect($ticket->messages->first())->toBeInstanceOf(SupportMessage::class);
});

it('casts the category and status to enums', function () {
    $ticket = SupportTicket::factory()->create([
        'category' => SupportCategory::BugReport,
        'status' => SupportTicketStatus::Closed,
    ]);

    expect($ticket->category)->toBe(SupportCategory::BugReport);
    expect($ticket->status)->toBe(SupportTicketStatus::Closed);
});

it('casts the closer to an enum', function () {
    $ticket = SupportTicket::factory()->closed(SupportTicketCloser::Team)->create();

    expect($ticket->closed_by)->toBe(SupportTicketCloser::Team);
    expect($ticket->closed_at)->not->toBeNull();
});

it('encrypts the subject at rest', function () {
    $ticket = SupportTicket::factory()->create([
        'subject' => 'The One Where Ross Loses His Import',
    ]);

    expect($ticket->subject)->toBe('The One Where Ross Loses His Import');

    $raw = DB::table('support_tickets')->where('id', $ticket->id)->value('subject');
    expect(decrypt($raw, false))->toBe('The One Where Ross Loses His Import');
});
