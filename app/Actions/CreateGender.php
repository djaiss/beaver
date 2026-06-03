<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateGender
{
    private Gender $gender;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private string $name,
    ) {}

    public function execute(): Gender
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->log();

        return $this->gender;
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

        if (! in_array($member->role, [PermissionEnum::Owner->value, PermissionEnum::Editor->value], true)) {
            throw new ModelNotFoundException('Permission denied');
        }
    }

    private function create(): void
    {
        $maxPosition = $this->vault->genders()->max('position') ?? 0;

        $this->gender = Gender::query()->create([
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
            action: UserActionEnum::GenderCreation,
            description: sprintf('Created a gender called %s', $this->name),
        )->onQueue('low');
    }
}
