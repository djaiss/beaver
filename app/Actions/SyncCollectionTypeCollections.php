<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Set which collections may use a type. Ids that do not belong to the account
 * are ignored. Only owners and editors of the account may do so.
 */
class SyncCollectionTypeCollections
{
    /**
     * @param  array<int, int>  $collectionIds
     */
    public function __construct(
        private readonly User $user,
        private readonly CollectionType $collectionType,
        private array $collectionIds = [],
    ) {}

    public function execute(): CollectionType
    {
        $this->validate();
        $this->sync();
        $this->log();

        return $this->collectionType;
    }

    private function validate(): void
    {
        if (! $this->collectionType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sync(): void
    {
        $ids = $this->collectionType->account
            ->collections()
            ->whereIn('id', $this->collectionIds)
            ->pluck('id')
            ->all();

        $this->collectionType->collections()->sync($ids);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionTypeUpdate,
            parameters: ['name' => $this->collectionType->name],
        )->onQueue('low');
    }
}
