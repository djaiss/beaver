<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CatalogType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Update a collection type. Only owners and editors of its account may do so.
 */
class UpdateCatalogType
{
    public function __construct(
        private readonly User $user,
        private readonly CatalogType $catalogType,
        private string $name,
        private string $color = '#6B7280',
    ) {}

    public function execute(): CatalogType
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->catalogType;
    }

    private function validate(): void
    {
        if (! $this->catalogType->account->allowsManagementBy($this->user)) {
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
        $this->catalogType->name = $this->name;
        $this->catalogType->color = $this->color;
        $this->catalogType->updated_by_id = $this->user->id;
        $this->catalogType->updated_by_name = $this->user->getFullName();
        $this->catalogType->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CatalogTypeUpdate,
            parameters: ['name' => $this->catalogType->name],
        )->onQueue('low');
    }
}
