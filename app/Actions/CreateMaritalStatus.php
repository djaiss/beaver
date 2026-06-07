<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\MaritalStatus;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateMaritalStatus
{
    private MaritalStatus $maritalStatus;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private string $name,
    ) {}

    public function execute(): MaritalStatus
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->log();

        return $this->maritalStatus;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        $member = $this->user->memberOf($this->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }
    }

    private function create(): void
    {
        $maxPosition = $this->vault->maritalStatuses()->max('position') ?? 0;

        $this->maritalStatus = MaritalStatus::query()->create([
            'vault_id' => $this->vault->id,
            'name' => $this->name,
            'position' => $maxPosition + 1,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::MaritalStatusCreation,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
