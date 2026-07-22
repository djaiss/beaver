<?php

declare(strict_types=1);

use App\Mail\SupportTeamReply;
use App\Models\SupportTicket;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('sets the envelope subject', function () {
    $ticket = SupportTicket::factory()->create();

    $mailable = new SupportTeamReply(ticket: $ticket, reply: 'Anything.');

    expect($mailable->envelope()->subject)->toBe('You have a new reply on your support conversation');
});

it('renders the conversation subject and the reply', function () {
    $ticket = SupportTicket::factory()->create(['subject' => 'The One With The Failing Import']);

    $mailable = new SupportTeamReply(
        ticket: $ticket,
        reply: 'Try the import again, it should be fixed now.',
    );

    $rendered = $mailable->render();

    expect($rendered)->toContain('The One With The Failing Import');
    expect($rendered)->toContain('Try the import again, it should be fixed now.');
});
