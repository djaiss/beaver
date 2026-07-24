<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Soft delete a collection. Only owners and editors of its account may do so.
 */
class DestroyCatalog
{
    public function __construct(
        private readonly User $user,
        private readonly Catalog $catalog,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->catalog->delete();
    }

    private function validate(): void
    {
        if (! $this->userCanManageCatalogs()) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function userCanManageCatalogs(): bool
    {
        return in_array(
            $this->catalog->account->roleFor($this->user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CatalogDeletion,
            parameters: ['name' => $this->catalog->name],
        )->onQueue('low');
    }
}
