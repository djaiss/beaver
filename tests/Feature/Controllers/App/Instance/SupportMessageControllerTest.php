<?php

declare(strict_types=1);

use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('lets an instance administrator reply to a conversation', function () {
    Queue::fake();

    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ross = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $ross->id]);

    $response = $this->actingAs($monica)->post('instance-admin/support/'.$ticket->id.'/messages', [
        'body' => 'We are on it.',
    ]);

    $response->assertRedirect(route('instanceAdmin.support.index', ['status' => 'open', 'ticket' => $ticket->id], absolute: false));
    $this->assertDatabaseHas('support_messages', [
        'support_ticket_id' => $ticket->id,
        'user_id' => $monica->id,
        'is_from_team' => true,
    ]);
    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Answered);
});

it('validates the reply body', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->create();

    $this->actingAs($monica)->post('instance-admin/support/'.$ticket->id.'/messages', [
        'body' => '',
    ])->assertSessionHasErrors('body');
});

it('forbids a user who does not administer the instance from replying', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ticket = SupportTicket::factory()->create();

    $this->actingAs($rachel)->post('instance-admin/support/'.$ticket->id.'/messages', [
        'body' => 'Sneaky reply.',
    ])->assertNotFound();

    $this->assertDatabaseMissing('support_messages', ['support_ticket_id' => $ticket->id]);
});
