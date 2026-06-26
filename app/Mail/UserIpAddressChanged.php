<?php

declare(strict_types=1);

namespace App\Mail;

use App\Interfaces\HasEnvelope;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserIpAddressChanged extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $ip,
    ) {}

    public function envelope(): Envelope
    {
        $appName = config('app.name');

        return new Envelope(
            subject: "New sign-in detected on your {$appName} account",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.account.user-ip-changed-text',
            markdown: 'mail.account.user-ip-changed',
            with: [
                'email' => $this->user->email,
                'ip' => $this->ip,
            ],
        );
    }
}
