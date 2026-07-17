<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CustomFieldGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Rename a group of custom fields. Only owners and editors of the type's
 * account may do so.
 */
class UpdateCustomFieldGroup
{
    public function __construct(
        private readonly User $user,
        private readonly CustomFieldGroup $customFieldGroup,
        private string $name,
    ) {}

    public function execute(): CustomFieldGroup
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->customFieldGroup;
    }

    private function validate(): void
    {
        if (! $this->customFieldGroup->collectionType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->customFieldGroup->name = $this->name;
        $this->customFieldGroup->updated_by_id = $this->user->id;
        $this->customFieldGroup->updated_by_name = $this->user->getFullName();
        $this->customFieldGroup->save();
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
