<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\UserActionEnum;
use App\Models\Log;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogUserAction implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public ?Vault $vault,
        public User $user,
        public UserActionEnum $action,
        public string $description,
    ) {}

    /**
     * Log the user action in the logs table.
     */
    public function handle(): void
    {
        Log::query()->create([
            'vault_id' => $this->vault?->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->getFullName(),
            'action' => $this->action->value,
            'description' => $this->description,
        ]);

        $this->user->last_activity_at = now();
        $this->user->save();
    }
}
