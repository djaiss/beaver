<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\RelationshipTypeCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateRelationshipTypeCategory
{
    public function __construct(
        private readonly User $user,
        private readonly RelationshipTypeCategory $relationshipTypeCategory,
        private ?string $name,
        private readonly ?int $position = null,
    ) {}

    public function execute(): RelationshipTypeCategory
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->log();

        return $this->relationshipTypeCategory;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::nullablePlainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->relationshipTypeCategory->vault) === false) {
            throw new ModelNotFoundException('Relationship type category not found');
        }

        if ($this->user->memberOf($this->relationshipTypeCategory->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        $maxPosition = $this->relationshipTypeCategory->vault->relationshipTypeCategories()->max('position') ?? 0;
        if ($this->position !== null && ($this->position < 1 || $this->position > $maxPosition + 1)) {
            throw new ModelNotFoundException('Invalid position');
        }
    }

    private function update(): void
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->position !== null && $this->position !== $this->relationshipTypeCategory->position) {
            $this->reorderPositions();
            $data['position'] = $this->position;
        }

        $this->relationshipTypeCategory->update($data);
    }

    private function reorderPositions(): void
    {
        $oldPosition = $this->relationshipTypeCategory->position;
        $newPosition = $this->position;

        if ($newPosition > $oldPosition) {
            $this->relationshipTypeCategory->vault->relationshipTypeCategories()
                ->where('id', '!=', $this->relationshipTypeCategory->id)
                ->whereBetween('position', [$oldPosition + 1, $newPosition])
                ->decrement('position');
        } elseif ($newPosition < $oldPosition) {
            $this->relationshipTypeCategory->vault->relationshipTypeCategories()
                ->where('id', '!=', $this->relationshipTypeCategory->id)
                ->whereBetween('position', [$newPosition, $oldPosition - 1])
                ->increment('position');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->relationshipTypeCategory->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeCategoryUpdate,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
