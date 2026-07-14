<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\CollectionType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Update a collection type. Only owners and editors of its account may do so.
 */
class UpdateCollectionType
{
    public function __construct(
        private readonly User $user,
        private readonly CollectionType $collectionType,
        private string $name,
        private string $color = '#6B7280',
    ) {}

    public function execute(): CollectionType
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->collectionType;
    }

    private function validate(): void
    {
        if (! $this->collectionType->account->allowsManagementBy($this->user)) {
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
        $this->collectionType->name = $this->name;
        $this->collectionType->color = $this->color;
        $this->collectionType->updated_by_id = $this->user->id;
        $this->collectionType->updated_by_name = $this->user->getFullName();
        $this->collectionType->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionTypeUpdate,
            parameters: ['name' => $this->collectionType->name],
        )->onQueue('low');
    }
}
