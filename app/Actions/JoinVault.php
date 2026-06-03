<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Member;
use App\Models\Vault;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class JoinVault
{
    private Vault $vault;

    public function __construct(
        private readonly User $user,
        private string $invitationCode,
    ) {}

    public function execute(): Vault
    {
        $this->sanitize();
        $this->validate();
        $this->join();
        $this->log();

        return $this->vault;
    }

    private function sanitize(): void
    {
        $this->invitationCode = TextSanitizer::plainText($this->invitationCode);
    }

    private function validate(): void
    {
        try {
            $this->vault = Vault::query()
                ->where('invitation_code', $this->invitationCode)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw ValidationException::withMessages([
                'invitation_code' => 'Invalid invitation code',
            ]);
        }
        if ($this->user->isPartOfVault($this->vault)) {
            throw ValidationException::withMessages([
                'invitation_code' => 'You are already a member of this vault',
            ]);
        }
    }

    private function join(): void
    {
        Member::query()->create([
            'vault_id' => $this->vault->id,
            'user_id' => $this->user->id,
            'joined_at' => now(),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::VaultJoined,
            description: 'Joined vault called ' . $this->vault->name,
        )->onQueue('low');
    }
}
