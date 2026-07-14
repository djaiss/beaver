<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\PermissionEnum;
use App\Enums\UserActionEnum;
use App\Enums\VisibilityEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Collection;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a collection within an account. Only owners and editors may do so.
 */
class CreateCollection
{
    private Collection $collection;

    /**
     * @param  array<string, mixed>|null  $settings
     */
    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
        private ?string $description = null,
        private ?string $emoji = null,
        private string $visibility = VisibilityEnum::Private->value,
        private ?string $currency = null,
        private ?array $settings = null,
    ) {}

    public function execute(): Collection
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->collection;
    }

    private function validate(): void
    {
        if (! $this->userCanManageCollections()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (VisibilityEnum::tryFrom($this->visibility) === null) {
            throw ValidationException::withMessages(['visibility' => 'Invalid visibility']);
        }
    }

    private function userCanManageCollections(): bool
    {
        return in_array(
            $this->account->roleFor($this->user),
            [PermissionEnum::Owner->value, PermissionEnum::Editor->value],
            true,
        );
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
        $this->emoji = TextSanitizer::nullablePlainText($this->emoji);
    }

    private function create(): void
    {
        $this->collection = Collection::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
            'description' => $this->description,
            'emoji' => $this->emoji,
            'visibility' => $this->visibility,
            'currency' => $this->currency,
            'settings' => $this->settings,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->collection->created_by_id = $this->user->id;
        $this->collection->created_by_name = $this->user->getFullName();
        $this->collection->updated_by_id = $this->user->id;
        $this->collection->updated_by_name = $this->user->getFullName();
        $this->collection->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CollectionCreation,
            parameters: ['name' => $this->collection->name],
        )->onQueue('low');
    }
}
