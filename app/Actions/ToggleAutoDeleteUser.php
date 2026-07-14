<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;

readonly class ToggleAutoDeleteUser
{
    public function __construct(
        private User $user,
        private bool $autoDeleteUser,
    ) {}

    /**
     * Toggle the auto delete user setting.
     */
    public function execute(): User
    {
        $this->update();
        $this->log();

        return $this->user;
    }

    private function update(): void
    {
        $this->user->update([
            'auto_delete_user' => $this->autoDeleteUser,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AutoDeleteUserUpdate,
            parameters: ['status' => $this->user->auto_delete_user ? 'enabled' : 'disabled'],
        )->onQueue('low');
    }
}
