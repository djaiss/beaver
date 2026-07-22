<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\ItemCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a condition's name. Only owners and editors of its account may do
 * so; system default conditions cannot be updated.
 */
class UpdateItemCondition
{
    public function __construct(
        private readonly User $user,
        private readonly ItemCondition $itemCondition,
        private string $name,
    ) {}

    public function execute(): ItemCondition
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->itemCondition;
    }

    private function validate(): void
    {
        if ($this->itemCondition->isSystemDefault()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->itemCondition->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->itemCondition->name = $this->name;
        $this->itemCondition->updated_by_id = $this->user->id;
        $this->itemCondition->updated_by_name = $this->user->getFullName();
        $this->itemCondition->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionUpdate,
            parameters: ['name' => $this->itemCondition->name],
        )->onQueue('low');
    }
}
