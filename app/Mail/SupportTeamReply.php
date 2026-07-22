<?php

declare(strict_types=1);

namespace App\Mail;

use App\Interfaces\HasEnvelope;
use App\Models\SupportTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupportTeamReply extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public SupportTicket $ticket,
        public string $reply,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have a new reply on your support conversation',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.support.team-reply-text',
            markdown: 'mail.support.team-reply',
            with: [
                'link' => route('support.tickets.show', $this->ticket),
                'subject' => $this->ticket->subject,
                'reply' => $this->reply,
            ],
        );
    }
}
