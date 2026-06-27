<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\RelationshipType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateRelationshipType
{
    public function __construct(
        private readonly User $user,
        private readonly RelationshipType $relationshipType,
        private ?string $name,
        private readonly bool $isDirected,
        private readonly ?int $position = null,
        private ?string $forwardName = null,
        private ?string $reverseName = null,
    ) {}

    public function execute(): RelationshipType
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->log();

        return $this->relationshipType;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::nullablePlainText($this->name);
        $this->forwardName = TextSanitizer::nullablePlainText($this->forwardName);
        $this->reverseName = TextSanitizer::nullablePlainText($this->reverseName);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->relationshipType->vault) === false) {
            throw new ModelNotFoundException('Relationship type not found');
        }

        if ($this->user->memberOf($this->relationshipType->vault)->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->relationshipType->relationshipTypeCategory->vault_id !== $this->relationshipType->vault_id) {
            throw new ModelNotFoundException('Relationship type category not found');
        }

        $maxPosition = $this->relationshipType->relationshipTypeCategory->relationshipTypes()->max('position') ?? 0;
        if ($this->position !== null && ($this->position < 1 || $this->position > $maxPosition + 1)) {
            throw new ModelNotFoundException('Invalid position');
        }
    }

    private function update(): void
    {
        $data = [
            'name' => $this->name,
            'is_directed' => $this->isDirected,
        ];

        if ($this->isDirected === false) {
            $data['forward_name'] = null;
            $data['reverse_name'] = null;
        } elseif ($this->forwardName !== null && $this->reverseName !== null) {
            $data['forward_name'] = $this->forwardName;
            $data['reverse_name'] = $this->reverseName;
        }

        if ($this->position !== null && $this->position !== $this->relationshipType->position) {
            $this->reorderPositions();
            $data['position'] = $this->position;
        }

        $this->relationshipType->update($data);
    }

    private function reorderPositions(): void
    {
        $oldPosition = $this->relationshipType->position;
        $newPosition = $this->position;
        $relationshipTypes = $this->relationshipType->relationshipTypeCategory->relationshipTypes()
            ->where('id', '!=', $this->relationshipType->id);

        if ($newPosition > $oldPosition) {
            $relationshipTypes
                ->whereBetween('position', [$oldPosition + 1, $newPosition])
                ->decrement('position');
        } elseif ($newPosition < $oldPosition) {
            $relationshipTypes
                ->whereBetween('position', [$newPosition, $oldPosition - 1])
                ->increment('position');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->relationshipType->vault,
            user: $this->user,
            action: UserActionEnum::RelationshipTypeUpdate,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
