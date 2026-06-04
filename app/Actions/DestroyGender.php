<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyGender
{
    private readonly string $genderName;

    public function __construct(
        private readonly User $user,
        private readonly Gender $gender,
    ) {
        $this->genderName = $this->gender->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->gender->vault) === false) {
            throw new ModelNotFoundException('Gender not found');
        }

        $member = $this->user->memberOf($this->gender->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }
    }

    private function delete(): void
    {
        $this->gender->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->gender->vault,
            user: $this->user,
            action: UserActionEnum::GenderDeletion,
            parameters: ['name' => $this->genderName],
        )->onQueue('low');
    }
}
