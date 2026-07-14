<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a location, cascading to its nested child locations. Only owners
 * and editors of its account may do so.
 */
class DestroyLocation
{
    public function __construct(
        private readonly User $user,
        private readonly Location $location,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->deleteDescendants($this->location);
        $this->location->delete();
    }

    private function validate(): void
    {
        if (! $this->location->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function deleteDescendants(Location $location): void
    {
        foreach ($location->children as $child) {
            $this->deleteDescendants($child);
            $child->delete();
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::LocationDeletion,
            parameters: ['name' => $this->location->name],
        )->onQueue('low');
    }
}
