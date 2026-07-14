<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\CollectionType;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a collection type within an account. Only owners and editors may do so.
 */
class CreateCollectionType
{
    private CollectionType $collectionType;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
        private string $color = '#6B7280',
    ) {}

    public function execute(): CollectionType
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->collectionType;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
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

    private function create(): void
    {
        $this->collectionType = CollectionType::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
            'color' => $this->color,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->collectionType->created_by_id = $this->user->id;
        $this->collectionType->created_by_name = $this->user->getFullName();
        $this->collectionType->updated_by_id = $this->user->id;
        $this->collectionType->updated_by_name = $this->user->getFullName();
        $this->collectionType->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionTypeCreation,
            parameters: ['name' => $this->collectionType->name],
        )->onQueue('low');
    }
}
