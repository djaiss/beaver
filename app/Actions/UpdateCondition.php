<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a condition's name. Only owners and editors of its account may do
 * so; system default conditions cannot be updated.
 */
class UpdateCondition
{
    public function __construct(
        private readonly User $user,
        private readonly Condition $condition,
        private string $name,
    ) {}

    public function execute(): Condition
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->condition;
    }

    private function validate(): void
    {
        if ($this->condition->isSystemDefault()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (! $this->condition->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->condition->name = $this->name;
        $this->condition->updated_by_id = $this->user->id;
        $this->condition->updated_by_name = $this->user->getFullName();
        $this->condition->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionUpdate,
            parameters: ['name' => $this->condition->name],
        )->onQueue('low');
    }
}
