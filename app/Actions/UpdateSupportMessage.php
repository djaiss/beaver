<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Edit the body of a support message.
 * Only the user who wrote the message may edit it.
 */
class UpdateSupportMessage
{
    public function __construct(
        private readonly User $user,
        private readonly SupportMessage $message,
        private string $body,
    ) {}

    public function execute(): SupportMessage
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->message;
    }

    private function validate(): void
    {
        if ($this->message->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Support message not found');
        }
    }

    private function sanitize(): void
    {
        $this->body = TextSanitizer::plainText($this->body);
    }

    private function update(): void
    {
        $this->message->body = $this->body;
        $this->message->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SupportMessageUpdate,
        )->onQueue('low');
    }
}
