<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\SpecialDate;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroySpecialDate
{
    private readonly string $specialDateName;

    public function __construct(
        private readonly User $user,
        private readonly SpecialDate $specialDate,
    ) {
        $this->specialDateName = $this->specialDate->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->specialDate->vault) === false) {
            throw new ModelNotFoundException('Special date not found');
        }

        if ($this->user->memberOf($this->specialDate->vault)->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->specialDate->person->vault_id !== $this->specialDate->vault_id) {
            throw new ModelNotFoundException('Person not found');
        }
    }

    private function delete(): void
    {
        $this->specialDate->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->specialDate->vault,
            user: $this->user,
            action: UserActionEnum::SpecialDateDeletion,
            parameters: ['name' => $this->specialDateName],
        )->onQueue('low');
    }
}
