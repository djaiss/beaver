<?php

declare(strict_types=1);

namespace App\Mail;

use App\Interfaces\HasEnvelope;
use App\Models\Testimonial;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestimonialPublished extends Mailable implements HasEnvelope, ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Testimonial $testimonial,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Your :name testimonial is live 🎉', ['name' => config('app.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.testimonial.published-text',
            markdown: 'mail.testimonial.published',
            with: [
                'firstName' => $this->testimonial->user->first_name,
            ],
        );
    }
}
