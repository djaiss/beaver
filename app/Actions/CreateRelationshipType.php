<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CreateRelationshipType
{
    private RelationshipType $relationshipType;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private readonly RelationshipTypeCategory $relationshipTypeCategory,
        private string $key,
        private string $name,
        private readonly bool $isDirected = false,
    ) {}

    public function execute(): RelationshipType
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->log();

        return $this->relationshipType;
    }

    private function sanitize(): void
    {
        $this->key = TextSanitizer::plainText($this->key);
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        if ($this->user->memberOf($this->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->relationshipTypeCategory->vault_id !== $this->vault->id) {
            throw new ModelNotFoundException('Relationship type category not found');
        }

        if ($this->relationshipTypeCategory->relationshipTypes()->where('key', $this->key)->exists()) {
            throw new ModelNotFoundException('Relationship type key already exists');
        }
    }

    private function create(): void
    {
        $maxPosition = $this->relationshipTypeCategory->relationshipTypes()->max('position') ?? 0;

        $this->relationshipType = RelationshipType::query()->create([
            'vault_id' => $this->vault->id,
            'relationship_type_category_id' => $this->relationshipTypeCategory->id,
            'key' => $this->key,
            'name' => $this->name,
            'is_directed' => $this->isDirected,
            'position' => $maxPosition + 1,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeCreation,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
