<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\SupportTicketStatus;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Add a reply to a support conversation as the user who owns it. Replying always
 * moves the conversation back to open, whether it was answered by the team or
 * already closed, so a follow up never needs a fresh conversation.
 */
class CreateSupportMessage
{
    private SupportMessage $message;

    public function __construct(
        private readonly User $user,
        private readonly SupportTicket $ticket,
        private string $body,
    ) {}

    public function execute(): SupportMessage
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->reopen();
        $this->log();

        return $this->message;
    }

    private function validate(): void
    {
        if ($this->ticket->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Support conversation not found');
        }
    }

    private function sanitize(): void
    {
        $this->body = TextSanitizer::plainText($this->body);
    }

    private function create(): void
    {
        $this->message = SupportMessage::query()->create([
            'support_ticket_id' => $this->ticket->id,
            'user_id' => $this->user->id,
            'body' => $this->body,
        ]);
    }

    private function reopen(): void
    {
        if ($this->ticket->status === SupportTicketStatus::Open) {
            return;
        }

        $this->ticket->status = SupportTicketStatus::Open;
        $this->ticket->closed_by = null;
        $this->ticket->closed_at = null;
        $this->ticket->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SupportMessageCreation,
        )->onQueue('low');
    }
}
