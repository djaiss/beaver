<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\MaritalStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyMaritalStatus
{
    private readonly string $maritalStatusName;

    public function __construct(
        private readonly User $user,
        private readonly MaritalStatus $maritalStatus,
    ) {
        $this->maritalStatusName = $this->maritalStatus->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->maritalStatus->vault) === false) {
            throw new ModelNotFoundException('Marital status not found');
        }

        $member = $this->user->memberOf($this->maritalStatus->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }
    }

    private function delete(): void
    {
        $this->maritalStatus->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->maritalStatus->vault,
            user: $this->user,
            action: UserActionEnum::MaritalStatusDeletion,
            parameters: ['name' => $this->maritalStatusName],
        )->onQueue('low');
    }
}
