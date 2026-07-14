<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a type, cascading to its custom fields and collection links. Only
 * owners and editors of its account may do so.
 */
class DestroyType
{
    public function __construct(
        private readonly User $user,
        private readonly Type $type,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->type->delete();
    }

    private function validate(): void
    {
        if (! $this->type->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TypeDeletion,
            parameters: ['name' => $this->type->name],
        )->onQueue('low');
    }
}
