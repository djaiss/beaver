<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class CreateRelationshipTypeCategory
{
    private RelationshipTypeCategory $relationshipTypeCategory;

    public function __construct(
        private readonly User $user,
        private readonly Vault $vault,
        private ?string $key,
        private string $name,
    ) {}

    public function execute(): RelationshipTypeCategory
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->log();

        return $this->relationshipTypeCategory;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $randomKey = Str::lower(Str::random(16));
        $this->key = $this->key === null
            ? "custom-{$randomKey}"
            : TextSanitizer::plainText($this->key);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->vault) === false) {
            throw new ModelNotFoundException('Vault not found');
        }

        if ($this->user->memberOf($this->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->vault->relationshipTypeCategories()->where('key', $this->key)->exists()) {
            throw new ModelNotFoundException('Relationship type category key already exists');
        }
    }

    private function create(): void
    {
        $maxPosition = $this->vault->relationshipTypeCategories()->max('position') ?? 0;

        $this->relationshipTypeCategory = RelationshipTypeCategory::query()->create([
            'vault_id' => $this->vault->id,
            'key' => $this->key,
            'name' => $this->name,
            'position' => $maxPosition + 1,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeCategoryCreation,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
