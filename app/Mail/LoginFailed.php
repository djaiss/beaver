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

class LoginFailed extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct() {}

    public function envelope(): Envelope
    {
        $appName = config('app.name');

        return new Envelope(
            subject: "Login attempt on {$appName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.auth.login-failed-text',
            markdown: 'mail.auth.login-failed',
        );
    }
}
