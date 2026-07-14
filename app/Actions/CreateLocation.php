<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a location within an account. Only owners and editors may do so.
 */
class CreateLocation
{
    private Location $location;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
        private ?int $parentId = null,
        private ?string $emoji = null,
    ) {}

    public function execute(): Location
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->location;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->parentId !== null && ! $this->account->locations()->whereKey($this->parentId)->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'Invalid parent location']);
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->emoji = TextSanitizer::nullablePlainText($this->emoji);
    }

    private function create(): void
    {
        $this->location = Location::query()->create([
            'account_id' => $this->account->id,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'emoji' => $this->emoji,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->location->created_by_id = $this->user->id;
        $this->location->created_by_name = $this->user->getFullName();
        $this->location->updated_by_id = $this->user->id;
        $this->location->updated_by_name = $this->user->getFullName();
        $this->location->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::LocationCreation,
            parameters: ['name' => $this->location->name],
        )->onQueue('low');
    }
}
