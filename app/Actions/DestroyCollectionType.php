<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a collection type, cascading to its custom fields and collection
 * links. Only owners and editors of its account may do so.
 */
class DestroyCollectionType
{
    public function __construct(
        private readonly User $user,
        private readonly CollectionType $collectionType,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->collectionType->delete();
    }

    private function validate(): void
    {
        if (! $this->collectionType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionTypeDeletion,
            parameters: ['name' => $this->collectionType->name],
        )->onQueue('low');
    }
}
