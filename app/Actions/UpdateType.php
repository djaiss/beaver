<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Type;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Update a type. Only owners and editors of its account may do so.
 */
class UpdateType
{
    public function __construct(
        private readonly User $user,
        private readonly Type $type,
        private string $name,
        private string $color = '#6B7280',
    ) {}

    public function execute(): Type
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->type;
    }

    private function validate(): void
    {
        if (! $this->type->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if (preg_match('/^#[0-9A-Fa-f]{6}$/', $this->color) !== 1) {
            throw ValidationException::withMessages(['color' => 'Invalid color']);
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->type->name = $this->name;
        $this->type->color = $this->color;
        $this->type->updated_by_id = $this->user->id;
        $this->type->updated_by_name = $this->user->getFullName();
        $this->type->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TypeUpdate,
            parameters: ['name' => $this->type->name],
        )->onQueue('low');
    }
}
