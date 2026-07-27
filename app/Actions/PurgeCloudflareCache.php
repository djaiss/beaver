<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use App\Services\CloudflareCache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Drop every public page Cloudflare holds, so the next visitor gets a freshly
 * rendered one. Only an instance administrator may do this, and the action
 * checks the flag itself.
 */
class PurgeCloudflareCache
{
    public function __construct(
        private readonly User $user,
    ) {}

    /**
     * Returns whether Cloudflare accepted the purge, so the screen that asked
     * for it can say what happened rather than always claiming success.
     */
    public function execute(): bool
    {
        $this->validate();

        $purged = CloudflareCache::purgeEverything();

        $this->log();

        return $purged;
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('Site options not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CloudflareCachePurged,
        )->onQueue('low');
    }
}
