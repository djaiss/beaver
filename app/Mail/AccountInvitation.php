<?php

declare(strict_types=1);

namespace App\Mail;

use App\Interfaces\HasEnvelope;
use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountInvitation extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Invitation $invitation,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been invited to join an account on '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.account.invitation-text',
            markdown: 'mail.account.invitation',
            with: [
                'link' => route('invitations.show', $this->invitation->token),
                'accountName' => $this->invitation->account->name,
            ],
        );
    }
}
