<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\PopulateVault;
use App\Models\Member;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Create a vault for a user.
 * The user will be added to the vault as the first user.
 */
class CreateVault
{
    private Vault $vault;

    public function __construct(
        public User $user,
        public string $name,
    ) {}

    public function execute(): Vault
    {
        $this->sanitize();
        $this->validate();

        DB::transaction(function (): void {
            $this->create();
            $this->addMembership();
            $this->populate();
            $this->log();
        });

        return $this->vault;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        // make sure the vault name doesn't contain any special characters
        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->name), [0, false], true)) {
            throw ValidationException::withMessages([
                'vault_name' => 'Vault name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        // make sure the vault name is not part of a reserved list of keywords
        $reservedNames = config('app.reserved_vault_keywords', []);
        if (Str::is($reservedNames, Str::lower($this->name))) {
            throw ValidationException::withMessages([
                'vault_name' => 'Vault name cannot contain reserved words like admin, support, contact, etc.',
            ]);
        }
    }

    private function create(): void
    {
        $this->vault = Vault::query()->create([
            'name' => $this->name,
            'invitation_code' => Str::random(64),
        ]);
    }

    private function addMembership(): void
    {
        Member::query()->create([
            'vault_id' => $this->vault->id,
            'user_id' => $this->user->id,
            'joined_at' => now(),
            'role' => PermissionEnum::Owner->value,
        ]);
    }

    private function populate(): void
    {
        PopulateVault::dispatch($this->vault)->onQueue('low');
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::VaultCreation,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
