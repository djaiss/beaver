<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\FieldTypeEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CustomField;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Update a custom field, including its position within the type. Only owners
 * and editors of the type's account may do so.
 */
class UpdateCustomField
{
    /**
     * @param  array<int, mixed>|null  $options
     */
    public function __construct(
        private readonly User $user,
        private readonly CustomField $customField,
        private string $name,
        private string $fieldType = FieldTypeEnum::Text->value,
        private ?array $options = null,
        private int $position = 1,
    ) {}

    public function execute(): CustomField
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->customField;
    }

    private function validate(): void
    {
        if (! $this->customField->catalogType->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (FieldTypeEnum::tryFrom($this->fieldType) === null) {
            throw ValidationException::withMessages(['field_type' => 'Invalid field type']);
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->customField->name = $this->name;
        $this->customField->field_type = FieldTypeEnum::from($this->fieldType);
        $this->customField->options = $this->options;
        $this->customField->position = $this->position;
        $this->customField->updated_by_id = $this->user->id;
        $this->customField->updated_by_name = $this->user->getFullName();
        $this->customField->save();
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
