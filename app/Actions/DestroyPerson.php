<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyPerson
{
    private readonly ?string $personName;

    public function __construct(
        private readonly User $user,
        private readonly Person $person,
    ) {
        $this->personName = $this->person->first_name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->person->vault) === false) {
            throw new ModelNotFoundException('Person not found');
        }

        $member = $this->user->memberOf($this->person->vault);

        if ($member->role === PermissionEnum::Viewer->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->person->can_be_deleted === false) {
            throw new ModelNotFoundException('Person cannot be deleted');
        }
    }

    private function delete(): void
    {
        $this->person->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->person->vault,
            user: $this->user,
            action: UserActionEnum::PersonDeletion,
            parameters: ['name' => $this->personName],
        )->onQueue('low');
    }
}
