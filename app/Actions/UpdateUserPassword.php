<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use SensitiveParameter;

readonly class UpdateUserPassword
{
    public function __construct(
        private User $user,
        #[SensitiveParameter]
        private string $currentPassword,
        #[SensitiveParameter]
        private string $newPassword,
    ) {}

    /**
     * Update the user password.
     */
    public function execute(): User
    {
        $this->validate();
        $this->update();
        $this->log();

        return $this->user;
    }

    private function validate(): void
    {
        if (! Hash::check($this->currentPassword, $this->user->password)) {
            throw new InvalidArgumentException('Current password is incorrect');
        }
    }

    private function update(): void
    {
        $this->user->update([
            'password' => Hash::make($this->newPassword),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::UpdateUserPassword,
        )->onQueue('low');
    }
}
