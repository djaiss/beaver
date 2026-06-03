<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Vault;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateVault
{
    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private string $name,
    ) {}

    public function execute(): Vault
    {
        $this->sanitize();
        $this->validate();
        $this->rename();
        $this->log();

        return $this->vault;
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

        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->name), [0, false], true)) {
            throw ValidationException::withMessages([
                'vault_name' => 'Vault name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        $member = $this->user->memberOf($this->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }
    }

    private function rename(): void
    {
        $this->vault->update([
            'name' => $this->name,
            'slug' => $this->vault->id.'-'.Str::of($this->name)->slug('-'),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::VaultUpdate,
            description: sprintf('Updated the vault called %s', $this->name),
        )->onQueue('low');
    }
}
