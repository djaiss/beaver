<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CustomFieldGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Move a group one step up or down by swapping its position with the
 * neighbouring group. Only owners and editors of the type's account may do so.
 */
class MoveCustomFieldGroup
{
    public function __construct(
        private readonly User $user,
        private readonly CustomFieldGroup $customFieldGroup,
        private string $direction = 'up',
    ) {}

    public function execute(): CustomFieldGroup
    {
        $this->validate();
        $this->swap();
        $this->log();

        return $this->customFieldGroup;
    }

    private function validate(): void
    {
        if (! $this->customFieldGroup->collectionType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function swap(): void
    {
        $groups = $this->customFieldGroup->collectionType
            ->customFieldGroups()
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $index = $groups->search(fn (CustomFieldGroup $group): bool => $group->id === $this->customFieldGroup->id);
        $target = $this->direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $groups->count()) {
            return;
        }

        $neighbour = $groups[$target];

        DB::transaction(function () use ($neighbour): void {
            [$this->customFieldGroup->position, $neighbour->position] = [$neighbour->position, $this->customFieldGroup->position];
            $this->customFieldGroup->save();
            $neighbour->save();
        });
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CustomFieldGroupUpdate,
            parameters: ['name' => $this->customFieldGroup->name],
        )->onQueue('low');
    }
}
