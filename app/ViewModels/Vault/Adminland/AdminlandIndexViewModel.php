<?php

declare(strict_types=1);

namespace App\ViewModels\Vault\Adminland;

use App\Models\Gender;
use App\Models\RelationshipType;
use App\Models\RelationshipTypeCategory;
use App\Models\Vault;
use Illuminate\Support\Collection;

class AdminlandIndexViewModel
{
    public function __construct(
        private readonly Vault $vault,
    ) {}

    public function vault(): Vault
    {
        return $this->vault;
    }

    public function genders(): Collection
    {
        return Gender::query()
            ->where('vault_id', $this->vault->id)
            ->orderBy('position')
            ->get()
            ->map(fn (Gender $gender) => (object) [
                'id' => $gender->id,
                'name' => $gender->name,
                'position' => $gender->position,
                'editUrl' => route('vault.adminland.genders.edit', ['vaultId' => $this->vault->id, 'gender' => $gender->id]),
                'destroyUrl' => route('vault.adminland.genders.destroy', ['vaultId' => $this->vault->id, 'gender' => $gender->id]),
                'positionUrl' => route('vault.adminland.genders.position.update', ['vaultId' => $this->vault->id, 'gender' => $gender->id]),
            ]);
    }

    public function relationshipTypeCategories(): Collection
    {
        return $this->vault
            ->relationshipTypeCategories()
            ->with(['relationshipTypes' => function ($query): void {
                $query->orderBy('position');
            }])
            ->orderBy('position')
            ->get()
            ->map(fn (RelationshipTypeCategory $category) => (object) [
                'id' => $category->id,
                'name' => $category->name,
                'can_be_deleted' => $category->can_be_deleted,
                'newRelationshipTypeUrl' => route('vault.adminland.relationship_types.new', ['vaultId' => $this->vault->id, 'relationshipTypeCategory' => $category->id]),
                'editUrl' => route('vault.adminland.relationship_type_categories.edit', ['vaultId' => $this->vault->id, 'relationshipTypeCategory' => $category->id]),
                'destroyUrl' => route('vault.adminland.relationship_type_categories.destroy', ['vaultId' => $this->vault->id, 'relationshipTypeCategory' => $category->id]),
                'relationshipTypes' => $category->relationshipTypes
                    ->map(fn (RelationshipType $relationshipType) => (object) [
                        'id' => $relationshipType->id,
                        'name' => $relationshipType->name,
                        'position' => $relationshipType->position,
                        'can_be_deleted' => $relationshipType->can_be_deleted,
                        'editUrl' => route('vault.adminland.relationship_types.edit', ['vaultId' => $this->vault->id, 'relationshipTypeCategory' => $category->id, 'relationshipType' => $relationshipType->id]),
                        'destroyUrl' => route('vault.adminland.relationship_types.destroy', ['vaultId' => $this->vault->id, 'relationshipTypeCategory' => $category->id, 'relationshipType' => $relationshipType->id]),
                    ]),
            ]);
    }

    public function url(): object
    {
        return (object) [
            'vaultShow' => route('vault.show', $this->vault),
            'update' => route('vault.adminland.update', ['vaultId' => $this->vault->id]),
            'index' => route('vault.adminland.index', ['vaultId' => $this->vault->id]),
            'security' => route('settings.security.index'),
            'manage' => route('vault.adminland.manage.index', ['vaultId' => $this->vault->id]),
            'newGender' => route('vault.adminland.genders.new', ['vaultId' => $this->vault->id]),
            'newRelationshipTypeCategory' => route('vault.adminland.relationship_type_categories.new', ['vaultId' => $this->vault->id]),
        ];
    }
}
