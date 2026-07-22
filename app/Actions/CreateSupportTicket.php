<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SupportCategory;
use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;

/**
 * Open a support conversation for a user, from a subject the user names, a
 * category, and a first message.
 */
class CreateSupportTicket
{
    private SupportTicket $ticket;

    public function __construct(
        private readonly User $user,
        private readonly SupportCategory $category,
        private string $subject,
        private string $message,
    ) {}

    public function execute(): SupportTicket
    {
        $this->sanitize();
        $this->createTicket();
        $this->createFirstMessage();
        $this->log();

        return $this->ticket;
    }

    private function sanitize(): void
    {
        $this->subject = TextSanitizer::plainText($this->subject);
        $this->message = TextSanitizer::plainText($this->message);
    }

    private function createTicket(): void
    {
        $this->ticket = SupportTicket::query()->create([
            'user_id' => $this->user->id,
            'subject' => $this->subject,
            'category' => $this->category,
            'status' => SupportTicketStatus::Open,
        ]);
    }

    private function createFirstMessage(): void
    {
        SupportMessage::query()->create([
            'support_ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'body' => $this->message,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SupportTicketCreation,
        )->onQueue('low');
    }
}
