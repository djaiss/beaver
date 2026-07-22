<?php

declare(strict_types=1);

use App\Enums\SupportTicketStatus;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

it('opens on the open tab when no tab is given', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $open = SupportTicket::factory()->create();
    $closed = SupportTicket::factory()->closed()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support');

    $response->assertOk();
    $response->assertViewIs('app.instance.support.index');
    expect($response->viewData('status'))->toBe('open');
    expect($response->viewData('tickets')->pluck('id')->all())->toBe([$open->id]);
    expect($response->viewData('tickets')->pluck('id')->all())->not->toContain($closed->id);
});

it('lists only closed conversations on the closed tab', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    SupportTicket::factory()->create();
    $closed = SupportTicket::factory()->closed()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support/closed');

    $response->assertOk();
    expect($response->viewData('tickets')->pluck('id')->all())->toBe([$closed->id]);
});

it('lists every conversation on the all tab', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $open = SupportTicket::factory()->create();
    $closed = SupportTicket::factory()->closed()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support/all');

    $response->assertOk();
    expect($response->viewData('tickets')->pluck('id')->sort()->values()->all())
        ->toBe(collect([$open->id, $closed->id])->sort()->values()->all());
});

it('spans every account on the instance', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $centralPerk = $this->createAccount('Central Perk');
    $ross = $this->createUser(['account_id' => $centralPerk->id]);
    $theirs = SupportTicket::factory()->create(['user_id' => $ross->id]);

    $response = $this->actingAs($monica)->get('instance-admin/support/all');

    $response->assertOk();
    expect($response->viewData('tickets')->pluck('id')->all())->toContain($theirs->id);
});

it('narrows the inbox to a search term', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $centralPerk = $this->createAccount('Central Perk');
    $ross = $this->createUser(['account_id' => $centralPerk->id]);

    $matching = SupportTicket::factory()->create(['user_id' => $ross->id, 'subject' => 'The espresso machine is broken']);
    $other = SupportTicket::factory()->create(['subject' => 'Cannot export my collection']);

    $response = $this->actingAs($monica)->get('instance-admin/support/all?search=espresso');

    $response->assertOk();
    expect($response->viewData('search'))->toBe('espresso');
    expect($response->viewData('tickets')->pluck('id')->all())->toBe([$matching->id]);
    expect($response->viewData('tickets')->pluck('id')->all())->not->toContain($other->id);
});

it('matches a search term against the requester account name', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $centralPerk = $this->createAccount('Central Perk');
    $ross = $this->createUser(['account_id' => $centralPerk->id]);
    $theirs = SupportTicket::factory()->create(['user_id' => $ross->id]);

    SupportTicket::factory()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support/all?search=central+perk');

    $response->assertOk();
    expect($response->viewData('tickets')->pluck('id')->all())->toBe([$theirs->id]);
});

it('selects the first conversation of the tab by default', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    SupportTicket::factory()->create();
    $newest = SupportTicket::factory()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support');

    $response->assertOk();
    expect($response->viewData('selected')->id)->toBe($newest->id);
});

it('selects the conversation named in the path', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $first = SupportTicket::factory()->create();
    SupportTicket::factory()->create();
    SupportMessage::factory()->create(['support_ticket_id' => $first->id]);

    $response = $this->actingAs($monica)->get('instance-admin/support/all/'.$first->id);

    $response->assertOk();
    expect($response->viewData('selected')->id)->toBe($first->id);
    $response->assertSee('Ticket #'.$first->id);
});

it('shows a blank state when a tab has no conversations', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $response = $this->actingAs($monica)->get('instance-admin/support');

    $response->assertOk();
    expect($response->viewData('tickets'))->toBeEmpty();
    expect($response->viewData('selected'))->toBeNull();
    $response->assertSee('No open conversations');
});

it('rejects an unknown tab', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);

    $this->actingAs($monica)->get('instance-admin/support/spam')->assertNotFound();
});

it('hides the inbox from a user who does not administer the instance', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);

    $this->actingAs($rachel)->get('instance-admin/support')->assertNotFound();
    $this->actingAs($rachel)->get('instance-admin/support/closed')->assertNotFound();
});

it('keeps an answered conversation in the open tab', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $answered = SupportTicket::factory()->answered()->create();

    $response = $this->actingAs($monica)->get('instance-admin/support/open');

    $response->assertOk();
    expect($response->viewData('tickets')->pluck('id')->all())->toContain($answered->id);
});

it('shows a team reply distinctly in the detail pane', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->answered()->create();
    SupportMessage::factory()->fromTeam()->create([
        'support_ticket_id' => $ticket->id,
        'body' => 'Sorted it for you.',
    ]);

    $response = $this->actingAs($monica)->get('instance-admin/support/open/'.$ticket->id);

    $response->assertOk();
    $response->assertSee('Sorted it for you.');
    $response->assertSee('Team');
});

it('closes a conversation from the panel', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->create();

    $response = $this->actingAs($monica)->put('instance-admin/support/'.$ticket->id, ['status' => 'closed']);

    $response->assertRedirect(route('instanceAdmin.support.index', ['status' => 'closed', 'ticket' => $ticket->id], absolute: false));
    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Closed);
});

it('reopens a conversation from the panel', function () {
    Queue::fake();
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->closed()->create();

    $response = $this->actingAs($monica)->put('instance-admin/support/'.$ticket->id, ['status' => 'open']);

    $response->assertRedirect(route('instanceAdmin.support.index', ['status' => 'open', 'ticket' => $ticket->id], absolute: false));
    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Open);
});

it('rejects a status it does not accept', function () {
    $monica = $this->createUser(['is_instance_administrator' => true]);
    $ticket = SupportTicket::factory()->create();

    $this->actingAs($monica)->put('instance-admin/support/'.$ticket->id, ['status' => 'answered'])
        ->assertSessionHasErrors('status');
});

it('forbids a user who does not administer the instance from changing a conversation', function () {
    $rachel = $this->createUser(['is_instance_administrator' => false]);
    $ticket = SupportTicket::factory()->create();

    $this->actingAs($rachel)->put('instance-admin/support/'.$ticket->id, ['status' => 'closed'])->assertNotFound();
    expect($ticket->fresh()->status)->toBe(SupportTicketStatus::Open);
});
