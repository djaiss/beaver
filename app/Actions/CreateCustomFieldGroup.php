<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\CustomFieldGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a group of custom fields on a type. New groups are appended after the
 * existing ones. Only owners and editors of the type's account may do so.
 */
class CreateCustomFieldGroup
{
    private CustomFieldGroup $customFieldGroup;

    public function __construct(
        private readonly User $user,
        private readonly CatalogType $catalogType,
        private string $name,
    ) {}

    public function execute(): CustomFieldGroup
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->customFieldGroup;
    }

    private function validate(): void
    {
        if (! $this->catalogType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function create(): void
    {
        $this->customFieldGroup = CustomFieldGroup::query()->create([
            'type_id' => $this->catalogType->id,
            'name' => $this->name,
            'position' => $this->nextPosition(),
        ]);
    }

    private function nextPosition(): int
    {
        return (int) $this->catalogType->customFieldGroups()->max('position') + 1;
    }

    private function stampAuthor(): void
    {
        $this->customFieldGroup->created_by_id = $this->user->id;
        $this->customFieldGroup->created_by_name = $this->user->getFullName();
        $this->customFieldGroup->updated_by_id = $this->user->id;
        $this->customFieldGroup->updated_by_name = $this->user->getFullName();
        $this->customFieldGroup->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CustomFieldGroupCreation,
            parameters: ['name' => $this->customFieldGroup->name],
        )->onQueue('low');
    }
}
