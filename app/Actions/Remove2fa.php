<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;

readonly class Remove2fa
{
    public function __construct(
        private User $user,
    ) {}

    public function execute(): void
    {
        $this->remove();
        $this->logUserAction();
    }

    private function remove(): void
    {
        $this->user->update([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TwoFaRemoval,
        )->onQueue('low');
    }
}
