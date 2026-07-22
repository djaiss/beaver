<?php

declare(strict_types=1);
use App\Enums\SupportCategory;
use App\Enums\SupportTicketCloser;
use App\Enums\SupportTicketStatus;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['support.enabled' => true]);
});

it('lists the users conversations', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'subject' => 'Ross needs help importing',
    ]);
    SupportMessage::factory()->create(['support_ticket_id' => $ticket->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/support');

    $response->assertOk();
    $response->assertSee('Ross needs help importing');
});

it('does not list another users conversations', function () {
    $user = $this->createUser();
    $otherTicket = SupportTicket::factory()->create(['subject' => 'Rachel private ticket']);
    SupportMessage::factory()->create(['support_ticket_id' => $otherTicket->id]);

    $response = $this->actingAs($user)->get('/support');

    $response->assertOk();
    $response->assertDontSee('Rachel private ticket');
});

it('shows the new conversation form', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)->get('/support/new');

    $response->assertOk();
    $response->assertSee('What do you need help with?');
    $response->assertSee('Actually, by me.');
    $response->assertSee('Regis');
    $response->assertSee('images/regis.png');
    $response->assertSee('Subject');
});

it('opens a conversation', function () {
    Queue::fake();

    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/support/new')
        ->post('/support', [
            'category' => SupportCategory::BugReport->value,
            'subject' => 'Import keeps timing out',
            'message' => 'The import keeps timing out on my collection.',
        ]);

    $this->assertDatabaseCount('support_tickets', 1);
    $ticket = SupportTicket::query()->first();

    $response->assertRedirect('/support/'.$ticket->id);
    expect($ticket->user_id)->toBe($user->id);
    expect($ticket->subject)->toBe('Import keeps timing out');
    expect($ticket->category)->toBe(SupportCategory::BugReport);
    expect($ticket->messages()->count())->toBe(1);
});

it('validates the new conversation', function () {
    $user = $this->createUser();

    $response = $this->actingAs($user)
        ->from('/support/new')
        ->post('/support', [
            'category' => 'not-a-category',
            'subject' => '',
            'message' => '',
        ]);

    $response->assertSessionHasErrors(['category', 'subject', 'message']);
    $this->assertDatabaseCount('support_tickets', 0);
});

it('shows a conversation thread', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'subject' => 'The One With The Timeout',
    ]);
    SupportMessage::factory()->create([
        'support_ticket_id' => $ticket->id,
        'user_id' => $user->id,
        'body' => 'Here is what happened.',
    ]);

    $response = $this->actingAs($user)->get('/support/'.$ticket->id);

    $response->assertOk();
    $response->assertSee('The One With The Timeout');
    $response->assertSee('Here is what happened.');
});

it('cannot view another users conversation', function () {
    $user = $this->createUser();
    $otherTicket = SupportTicket::factory()->create();

    $response = $this->actingAs($user)->get('/support/'.$otherTicket->id);

    $response->assertNotFound();
});

it('closes a conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create([
        'user_id' => $user->id,
        'status' => SupportTicketStatus::Open,
    ]);

    $response = $this->actingAs($user)->put('/support/'.$ticket->id);

    $response->assertRedirect('/support/'.$ticket->id);
    $fresh = $ticket->fresh();
    expect($fresh->status)->toBe(SupportTicketStatus::Closed);
    expect($fresh->closed_by)->toBe(SupportTicketCloser::User);
    expect($fresh->closed_at)->not->toBeNull();
});

it('shows a team reply as Support in the thread', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->answered()->create(['user_id' => $user->id]);
    SupportMessage::factory()->fromTeam()->create([
        'support_ticket_id' => $ticket->id,
        'body' => 'Here is the fix, Ross.',
    ]);

    $response = $this->actingAs($user)->get(route('support.tickets.show', $ticket));

    $response->assertOk();
    $response->assertSee('Here is the fix, Ross.');
    $response->assertSee('Support');
});

it('lets the user reply while the team has answered', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->answered()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->get(route('support.tickets.show', $ticket));

    $response->assertOk();
    $response->assertSee('Write a reply…');
});

it('shows who closed the conversation in the thread', function () {
    $user = $this->createUser();
    $ticket = SupportTicket::factory()->closed(SupportTicketCloser::Team)->create([
        'user_id' => $user->id,
    ]);
    SupportMessage::factory()->create(['support_ticket_id' => $ticket->id, 'user_id' => $user->id]);

    $response = $this->actingAs($user)->get('/support/'.$ticket->id);

    $response->assertOk();
    $response->assertSee('Closed by');
    $response->assertSee('the support team');
});

it('deletes a conversation', function () {
    Queue::fake();

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete('/support/'.$ticket->id);

    $response->assertRedirect('/support');
    $this->assertModelMissing($ticket);
});

it('cannot delete another users conversation', function () {
    $user = $this->createUser();
    $otherTicket = SupportTicket::factory()->create();

    $response = $this->actingAs($user)->delete('/support/'.$otherTicket->id);

    $response->assertNotFound();
    $this->assertModelExists($otherTicket);
});

it('answers 404 on every support route when the section is disabled', function () {
    config(['support.enabled' => false]);

    $user = $this->createUser();
    $ticket = SupportTicket::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)->get('/support')->assertNotFound();
    $this->actingAs($user)->get('/support/new')->assertNotFound();
    $this->actingAs($user)->post('/support', [
        'category' => SupportCategory::Billing->value,
        'message' => 'Hello',
    ])->assertNotFound();
    $this->actingAs($user)->get('/support/'.$ticket->id)->assertNotFound();
    $this->actingAs($user)->put('/support/'.$ticket->id)->assertNotFound();
    $this->actingAs($user)->delete('/support/'.$ticket->id)->assertNotFound();
});
