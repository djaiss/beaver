<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class DestroyRelationshipTypeCategory
{
    private readonly string $relationshipTypeCategoryName;

    public function __construct(
        private readonly User $user,
        private readonly RelationshipTypeCategory $relationshipTypeCategory,
    ) {
        $this->relationshipTypeCategoryName = $this->relationshipTypeCategory->name ?? $this->relationshipTypeCategory->key;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->relationshipTypeCategory->vault) === false) {
            throw new ModelNotFoundException('Relationship type category not found');
        }

        if ($this->user->memberOf($this->relationshipTypeCategory->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->relationshipTypeCategory->can_be_deleted === false) {
            throw new ModelNotFoundException('Relationship type category cannot be deleted');
        }
    }

    private function delete(): void
    {
        DB::transaction(function (): void {
            $this->relationshipTypeCategory->relationshipTypes()->delete();
            $this->relationshipTypeCategory->delete();
        });
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->relationshipTypeCategory->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeCategoryDeletion,
            parameters: ['name' => $this->relationshipTypeCategoryName],
        )->onQueue('low');
    }
}
