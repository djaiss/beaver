<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Soft delete a collection. Only owners and editors of its account may do so.
 */
class DestroyCollection
{
    public function __construct(
        private readonly User $user,
        private readonly Collection $collection,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->collection->delete();
    }

    private function validate(): void
    {
        if (! $this->userCanManageCollections()) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function userCanManageCollections(): bool
    {
        return in_array(
            $this->collection->account->roleFor($this->user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionDeletion,
            parameters: ['name' => $this->collection->name],
        )->onQueue('low');
    }
}
