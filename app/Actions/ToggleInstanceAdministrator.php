<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Grant or revoke the instance administration for another user.
 */
class ToggleInstanceAdministrator
{
    public function __construct(
        private readonly User $user,
        private readonly User $userToToggle,
        private readonly bool $isInstanceAdministrator,
    ) {}

    public function execute(): User
    {
        $this->validate();
        $this->update();
        $this->log();

        return $this->userToToggle;
    }

    private function validate(): void
    {
        if (! $this->user->isInstanceAdministrator()) {
            throw new ModelNotFoundException('User not found');
        }

        // Dropping your own flag could leave an instance with no administrator
        // at all, and no way back in short of the artisan command.
        if ($this->user->id === $this->userToToggle->id) {
            throw new ModelNotFoundException('User not found');
        }
    }

    private function update(): void
    {
        $this->userToToggle->update([
            'is_instance_administrator' => $this->isInstanceAdministrator,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::InstanceAdministratorUpdate,
            parameters: [
                'email' => $this->userToToggle->email,
                'status' => $this->isInstanceAdministrator ? 'granted' : 'revoked',
            ],
        )->onQueue('low');
    }
}
