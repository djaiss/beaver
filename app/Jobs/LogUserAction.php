<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\UserActionEnum;
use App\Models\Log;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogUserAction implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public User $user,
        public UserActionEnum $action,
        public ?array $parameters = null,
    ) {}

    /**
     * Log the user action in the logs table.
     */
    public function handle(): void
    {
        Log::query()->create([
            'user_id' => $this->user->id,
            'user_name' => $this->user->getFullName(),
            'action' => $this->action->value,
            'parameters' => $this->parameters,
        ]);

        $this->user->last_activity_at = now();
        $this->user->save();
    }
}
