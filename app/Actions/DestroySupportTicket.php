<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a support conversation and its messages.
 * Only the user who owns the conversation may delete it.
 */
class DestroySupportTicket
{
    public function __construct(
        private readonly User $user,
        private readonly SupportTicket $ticket,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->ticket->messages()->delete();
        $this->ticket->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->ticket->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Support conversation not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SupportTicketDeletion,
        )->onQueue('low');
    }
}
