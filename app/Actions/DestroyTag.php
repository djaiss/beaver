<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a tag. Only owners and editors of its account may do so.
 */
class DestroyTag
{
    public function __construct(
        private readonly User $user,
        private readonly Tag $tag,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->tag->delete();
    }

    private function validate(): void
    {
        if (! $this->tag->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TagDeletion,
            parameters: ['name' => $this->tag->name],
        )->onQueue('low');
    }
}
