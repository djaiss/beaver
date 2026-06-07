<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\MaritalStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateMaritalStatus
{
    public function __construct(
        private readonly User $user,
        private readonly MaritalStatus $maritalStatus,
        private ?string $name,
        private readonly ?int $position = null,
    ) {}

    public function execute(): MaritalStatus
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->log();

        return $this->maritalStatus;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::nullablePlainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->maritalStatus->vault) === false) {
            throw new ModelNotFoundException('Marital status not found');
        }

        $member = $this->user->memberOf($this->maritalStatus->vault);

        if ($member->role !== PermissionEnum::Owner->value) {
            throw new ModelNotFoundException('Permission denied');
        }

        if ($this->position !== null) {
            $maxPosition = $this->maritalStatus->vault->maritalStatuses()->max('position') ?? 0;
            if ($this->position < 1 || $this->position > $maxPosition + 1) {
                throw new ModelNotFoundException('Invalid position');
            }
        }
    }

    private function update(): void
    {
        $data = [
            'name' => $this->name,
        ];

        if ($this->position !== null && $this->position !== $this->maritalStatus->position) {
            $this->reorderPositions();
            $data['position'] = $this->position;
        }

        $this->maritalStatus->update($data);
    }

    private function reorderPositions(): void
    {
        $oldPosition = $this->maritalStatus->position;
        $newPosition = $this->position;

        if ($newPosition > $oldPosition) {
            $this->maritalStatus->vault->maritalStatuses()
                ->where('id', '!=', $this->maritalStatus->id)
                ->whereBetween('position', [$oldPosition + 1, $newPosition])
                ->decrement('position');
        } elseif ($newPosition < $oldPosition) {
            $this->maritalStatus->vault->maritalStatuses()
                ->where('id', '!=', $this->maritalStatus->id)
                ->whereBetween('position', [$newPosition, $oldPosition - 1])
                ->increment('position');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->maritalStatus->vault,
            user: $this->user,
            action: UserActionEnum::MaritalStatusUpdate,
            parameters: ['name' => $this->name],
        )->onQueue('low');
    }
}
