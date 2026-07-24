<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a collection type, cascading to its custom fields and collection
 * links. Only owners and editors of its account may do so.
 */
class DestroyCatalogType
{
    public function __construct(
        private readonly User $user,
        private readonly CatalogType $catalogType,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->catalogType->delete();
    }

    private function validate(): void
    {
        if (! $this->catalogType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CatalogTypeDeletion,
            parameters: ['name' => $this->catalogType->name],
        )->onQueue('low');
    }
}
