<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyVault
{
    private readonly string $vaultName;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
    ) {
        $this->vaultName = $this->vault->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        $member = $this->user->memberOf($this->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Vault not found');
        }
    }

    private function delete(): void
    {
        $this->vault->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: null,
            user: $this->user,
            action: UserActionEnum::VaultDeletion,
            parameters: ['name' => $this->vaultName],
        )->onQueue('low');
    }
}
