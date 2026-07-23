<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\ItemCondition;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a condition within an account. Only owners and editors may do so.
 */
class CreateItemCondition
{
    private ItemCondition $itemCondition;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
    ) {}

    public function execute(): ItemCondition
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->itemCondition;
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
        // A new condition is appended after the account's existing ones. The
        // order (best to worst) is what lets a return be flagged as worse than
        // the copy left in, so a fresh condition starts at the bottom.
        $position = (int) $this->account->itemConditions()->max('position') + 1;

        $this->itemCondition = ItemCondition::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
            'position' => $position,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->itemCondition->created_by_id = $this->user->id;
        $this->itemCondition->created_by_name = $this->user->getFullName();
        $this->itemCondition->updated_by_id = $this->user->id;
        $this->itemCondition->updated_by_name = $this->user->getFullName();
        $this->itemCondition->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ConditionCreation,
            parameters: ['name' => $this->itemCondition->name],
        )->onQueue('low');
    }
}
