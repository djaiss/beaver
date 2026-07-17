<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CustomField;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Move a custom field one step up or down by swapping its position with the
 * neighbouring field in its group. Moves stop at the group boundary, as a field
 * never changes group. Only owners and editors of the type's account may do so.
 */
class MoveCustomField
{
    public function __construct(
        private readonly User $user,
        private readonly CustomField $customField,
        private string $direction = 'up',
    ) {}

    public function execute(): CustomField
    {
        $this->validate();
        $this->swap();
        $this->log();

        return $this->customField;
    }

    private function validate(): void
    {
        if (! $this->customField->collectionType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function swap(): void
    {
        $fields = $this->customField->collectionType
            ->customFields()
            ->where('group_id', $this->customField->group_id)
            ->orderBy('position')
            ->orderBy('id')
            ->get();

        $index = $fields->search(fn (CustomField $field): bool => $field->id === $this->customField->id);
        $target = $this->direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $fields->count()) {
            return;
        }

        $neighbour = $fields[$target];

        DB::transaction(function () use ($neighbour): void {
            [$this->customField->position, $neighbour->position] = [$neighbour->position, $this->customField->position];
            $this->customField->save();
            $neighbour->save();
        });
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CustomFieldUpdate,
            parameters: ['name' => $this->customField->name],
        )->onQueue('low');
    }
}
