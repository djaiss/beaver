<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Set which collections may use a type. Ids that do not belong to the account
 * are ignored. Only owners and editors of the account may do so.
 */
class SyncCatalogTypeCatalogs
{
    /**
     * @param  array<int, int>  $catalogIds
     */
    public function __construct(
        private readonly User $user,
        private readonly CatalogType $catalogType,
        private array $catalogIds = [],
    ) {}

    public function execute(): CatalogType
    {
        $this->validate();
        $this->sync();
        $this->log();

        return $this->catalogType;
    }

    private function validate(): void
    {
        if (! $this->catalogType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sync(): void
    {
        $ids = $this->catalogType->account
            ->catalogs()
            ->whereIn('id', $this->catalogIds)
            ->pluck('id')
            ->all();

        $this->catalogType->catalogs()->sync($ids);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CatalogTypeUpdate,
            parameters: ['name' => $this->catalogType->name],
        )->onQueue('low');
    }
}
