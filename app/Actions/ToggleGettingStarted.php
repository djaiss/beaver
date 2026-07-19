<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Show or hide the getting started screen for the whole account. The setting is
 * account wide rather than per user, so only owners may change it.
 */
class ToggleGettingStarted
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private readonly bool $show,
    ) {}

    public function execute(): Account
    {
        $this->validate();
        $this->update();
        $this->log();

        return $this->account;
    }

    private function validate(): void
    {
        if (! $this->account->isOwnedBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function update(): void
    {
        $this->account->show_getting_started = $this->show;
        $this->account->updated_by_id = $this->user->id;
        $this->account->updated_by_name = $this->user->getFullName();
        $this->account->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::GettingStartedUpdate,
            parameters: ['status' => $this->show ? 'shown' : 'hidden'],
        )->onQueue('low');
    }
}
