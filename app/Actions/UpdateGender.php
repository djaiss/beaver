<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Gender;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UpdateGender
{
    public function __construct(
        private readonly User $user,
        private readonly Gender $gender,
        private string $name,
        private readonly ?int $position = null,
    ) {}

    public function execute(): Gender
    {
        $this->sanitize();
        $this->validate();
        $this->update();
        $this->log();

        return $this->gender;
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function validate(): void
    {
        if ($this->user->isPartOfVault($this->gender->vault) === false) {
            throw new ModelNotFoundException('Gender not found');
        }

        $member = $this->user->memberOf($this->gender->vault);

        if (! in_array($member->role, [PermissionEnum::Owner->value, PermissionEnum::Editor->value], true)) {
            throw new ModelNotFoundException('Permission denied');
        }

        // position should not be less than 1 and not greater than the max position + 1 of the vault
        if ($this->position !== null) {
            $maxPosition = $this->gender->vault->genders()->max('position') ?? 0;
            if ($this->position < 1 || $this->position > $maxPosition + 1) {
                throw new ModelNotFoundException('Invalid position');
            }
        }
    }

    private function update(): void
    {
        $data = ['name' => $this->name];

        if ($this->position !== null && $this->position !== $this->gender->position) {
            $this->reorderPositions();
            $data['position'] = $this->position;
        }

        $this->gender->update($data);
    }

    private function reorderPositions(): void
    {
        $oldPosition = $this->gender->position;
        $newPosition = $this->position;

        // Moving down (e.g., from position 2 to position 5)
        if ($newPosition > $oldPosition) {
            $this->gender->vault->genders()
                ->where('id', '!=', $this->gender->id)
                ->whereBetween('position', [$oldPosition + 1, $newPosition])
                ->decrement('position');
        }
        // Moving up (e.g., from position 5 to position 2)
        elseif ($newPosition < $oldPosition) {
            $this->gender->vault->genders()
                ->where('id', '!=', $this->gender->id)
                ->whereBetween('position', [$newPosition, $oldPosition - 1])
                ->increment('position');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            vault: $this->gender->vault,
            user: $this->user,
            action: UserActionEnum::GenderUpdate,
            description: sprintf('Updated a gender to %s', $this->name),
        )->onQueue('low');
    }
}
