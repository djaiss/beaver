<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;

/**
 * Remove the avatar of a user, falling them back to the generated initials.
 */
class DestroyUserAvatar
{
    public function __construct(
        private readonly User $user,
    ) {}

    public function execute(): User
    {
        $path = $this->user->avatar_path;

        if ($path === null) {
            return $this->user;
        }

        $this->user->avatar_path = null;
        $this->user->save();

        // The files go last: deleting them cannot be rolled back.
        new DestroyUserAvatarFiles(path: $path)->execute();

        $this->log();

        return $this->user;
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AvatarDeletion,
        )->onQueue('low');
    }
}
