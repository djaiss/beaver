<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a condition within an account. Only owners and editors may do so.
 */
class CreateCondition
{
    private Condition $condition;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
    ) {}

    public function execute(): Condition
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->condition;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function create(): void
    {
        $this->condition = Condition::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->condition->created_by_id = $this->user->id;
        $this->condition->created_by_name = $this->user->getFullName();
        $this->condition->updated_by_id = $this->user->id;
        $this->condition->updated_by_name = $this->user->getFullName();
        $this->condition->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionCreation,
            parameters: ['name' => $this->condition->name],
        )->onQueue('low');
    }
}
