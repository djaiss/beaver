<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\ResponseCache\Facades\ResponseCache;

/**
 * Drop every response cached marketing page, so the next visitor gets a freshly
 * rendered one. Only an instance administrator may do this, and the action
 * checks the flag itself.
 */
class ClearResponseCache
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->clear();
        $this->log();
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('Site options not found');
        }
    }

    private function clear(): void
    {
        ResponseCache::clear();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ResponseCacheCleared,
        )->onQueue('low');
    }
}
