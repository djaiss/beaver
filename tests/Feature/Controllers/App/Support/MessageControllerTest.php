<?php

declare(strict_types=1);
use App\Enums\SupportTicketStatus;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['support.enabled' => true]);
});

it('adds a reply to the conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->from('/support/'.$ticket->id)
        ->post('/support/'.$ticket->id.'/messages', [
            'body' => 'Following up on this.',
        ]);

    $response->assertRedirect('/support/'.$ticket->id);
    expect($ticket->messages()->count())->toBe(1);
    expect($ticket->messages()->first()->body)->toBe('Following up on this.');
});

it('reopens a closed conversation on reply', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->closed()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post('/support/'.$ticket->id.'/messages', [
            'body' => 'One more thing.',
        ]);

    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Open);
});

it('validates the reply', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)
        ->from('/support/'.$ticket->id)
        ->post('/support/'.$ticket->id.'/messages', [
            'body' => '',
        ]);

    $response->assertSessionHasErrors('body');
    expect($ticket->messages()->count())->toBe(0);
});

it('cannot reply to another users conversation', function () {
    $user = $this->createUser();
    $otherTicket = SupportTicket::factory()->create();

    $response = $this->actingAs($user)
        ->post('/support/'.$otherTicket->id.'/messages', [
            'body' => 'Sneaky.',
        ]);

    $response->assertNotFound();
    expect($otherTicket->messages()->count())->toBe(0);
});

it('answers 404 when the section is disabled', function () {
    config(['support.enabled' => false]);

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post('/support/'.$ticket->id.'/messages', ['body' => 'Hello'])
        ->assertNotFound();
});
