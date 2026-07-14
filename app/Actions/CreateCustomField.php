<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\FieldTypeEnum;
use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CustomField;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a custom field on a type. New fields are appended after the existing
 * ones. Only owners and editors of the type's account may do so.
 */
class CreateCustomField
{
    private CustomField $customField;

    /**
     * @param  array<int, mixed>|null  $options
     */
    public function __construct(
        private readonly User $user,
        private readonly Type $type,
        private string $name,
        private string $fieldType = FieldTypeEnum::Text->value,
        private ?array $options = null,
    ) {}

    public function execute(): CustomField
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->customField;
    }

    private function validate(): void
    {
        if (! $this->type->account->allowsManagementBy($this->user)) {
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

    private function create(): void
    {
        $this->customField = CustomField::query()->create([
            'type_id' => $this->type->id,
            'name' => $this->name,
            'field_type' => $this->fieldType,
            'options' => $this->options,
            'position' => $this->nextPosition(),
        ]);
    }

    private function nextPosition(): int
    {
        return (int) $this->type->customFields()->max('position') + 1;
    }

    private function stampAuthor(): void
    {
        $this->customField->created_by_id = $this->user->id;
        $this->customField->created_by_name = $this->user->getFullName();
        $this->customField->updated_by_id = $this->user->id;
        $this->customField->updated_by_name = $this->user->getFullName();
        $this->customField->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CustomFieldCreation,
            parameters: ['name' => $this->customField->name],
        )->onQueue('low');
    }
}
