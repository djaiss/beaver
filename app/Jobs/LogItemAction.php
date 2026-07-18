<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\ItemActionEnum;
use App\Models\Item;
use App\Models\ItemLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class LogItemAction implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Item $item,
        public User $user,
        public ItemActionEnum $action,
        public ?array $parameters = null,
    ) {}

    /**
     * Log the action in the item_logs table. Unlike LogUserAction, this does
     * not stamp the user's last activity: the two jobs run side by side, and
     * that stamp belongs to the user trail alone.
     */
    public function handle(): void
    {
        ItemLog::query()->create([
            'item_id' => $this->item->id,
            'user_id' => $this->user->id,
            'user_name' => $this->user->getFullName(),
            'action' => $this->action->value,
            'parameters' => $this->parameters,
        ]);
    }
}
