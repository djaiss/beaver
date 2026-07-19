<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PhotoViewEnum;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Remember which layout a user last chose on the photos screen. The preference
 * belongs to the user rather than to the account, so two members of the same
 * account can look at the library differently.
 */
class UpdateUserPhotoView
{
    public function __construct(
        private readonly User $user,
        private string $view,
    ) {}

    public function execute(): User
    {
        $this->validate();

        return $this->update();
    }

    private function validate(): void
    {
        if (PhotoViewEnum::tryFrom($this->view) === null) {
            throw ValidationException::withMessages(['view' => 'Invalid view']);
        }
    }

    private function update(): User
    {
        $this->user->photos_view = PhotoViewEnum::from($this->view);
        $this->user->save();

        return $this->user;
    }
}
