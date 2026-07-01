<?php

declare(strict_types=1);

namespace App\Mail;

use App\Interfaces\HasEnvelope;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewLoginDetected extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $device,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New sign-in to your account',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.auth.new-login-text',
            markdown: 'mail.auth.new-login',
            with: [
                'device' => $this->device,
            ],
        );
    }
}
