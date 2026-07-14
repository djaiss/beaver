<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Rename an account. Only an owner may do so.
 */
class UpdateAccount
{
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
    ) {}

    public function execute(): Account
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->account;
    }

    private function validate(): void
    {
        if ($this->account->roleFor($this->user) !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->account->name = $this->name;
        $this->account->updated_by_id = $this->user->id;
        $this->account->updated_by_name = $this->user->getFullName();
        $this->account->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::AccountUpdate,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
