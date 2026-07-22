<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Set;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a set. Only owners and editors of its account may do so.
 */
class DestroySet
{
    public function __construct(
        private readonly User $user,
        private readonly Set $set,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->set->delete();
    }

    private function validate(): void
    {
        if (! $this->set->collection->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SetDeletion,
            parameters: ['name' => $this->set->name],
        )->onQueue('low');
    }
}
