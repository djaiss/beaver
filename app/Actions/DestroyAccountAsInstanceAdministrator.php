<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete any account on the instance, along with everything it owns. Distinct
 * from DestroyAccount, which requires the actor to own the account being
 * deleted. Here the actor administers the instance and need not be a member at
 * all.
 */
class DestroyAccountAsInstanceAdministrator
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->account->delete();
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('Account not found');
        }

        // Deleting your own account from here would take your user with it and
        // lock you out of the panel. Use the account settings for that.
        if ($this->user->account_id === $this->account->id) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::InstanceAccountDeletion,
            parameters: ['name' => $this->account->name],
        )->onQueue('low');
    }
}
