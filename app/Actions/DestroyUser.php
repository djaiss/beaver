<?php

declare(strict_types=1);

namespace App\Actions;

use App\Mail\UserDeleted;
use App\Models\User;
use App\Models\UserDeletionReason;
use Illuminate\Support\Facades\Mail;

class DestroyUser
{
    public function __construct(
        private readonly User $user,
        private readonly string $reason,
    ) {}

    public function execute(): void
    {
        $this->user->delete();
        $this->sendMail();
        $this->logUserDeletion();
    }

    private function sendMail(): void
    {
        Mail::to(config('app.account_deletion_notification_email'))
            ->queue(new UserDeleted(
                reason: $this->reason,
                activeSince: $this->user->created_at->format('Y-m-d'),
            ));
    }

    private function logUserDeletion(): void
    {
        UserDeletionReason::query()->create([
            'reason' => $this->reason,
        ]);
    }
}
