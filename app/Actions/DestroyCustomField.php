<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\CustomField;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a custom field. Only owners and editors of the type's account may do
 * so.
 */
class DestroyCustomField
{
    public function __construct(
        private readonly User $user,
        private readonly CustomField $customField,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->customField->delete();
    }

    private function validate(): void
    {
        if (! $this->customField->type->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CustomFieldDeletion,
            parameters: ['name' => $this->customField->name],
        )->onQueue('low');
    }
}
