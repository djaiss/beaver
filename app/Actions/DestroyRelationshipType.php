<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DestroyRelationshipType
{
    private readonly string $relationshipTypeName;

    public function __construct(
        private readonly User $user,
        private readonly RelationshipType $relationshipType,
    ) {
        $this->relationshipTypeName = $this->relationshipType->name;
    }

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->relationshipType->vault) === false) {
            throw new ModelNotFoundException('Relationship type not found');
        }

        if ($this->user->memberOf($this->relationshipType->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->relationshipType->can_be_deleted === false) {
            throw new ModelNotFoundException('Relationship type cannot be deleted');
        }
    }

    private function delete(): void
    {
        $this->relationshipType->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->relationshipType->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeDeletion,
            parameters: ['name' => $this->relationshipTypeName],
        )->onQueue('low');
    }
}
