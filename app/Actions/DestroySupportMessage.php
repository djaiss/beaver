<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a single support message.
 * Only the user who wrote the message may delete it.
 */
class DestroySupportMessage
{
    public function __construct(
        private readonly User $user,
        private readonly SupportMessage $message,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->message->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->message->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Support message not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SupportMessageDeletion,
        )->onQueue('low');
    }
}
