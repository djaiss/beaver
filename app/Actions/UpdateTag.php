<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a tag's name. Only owners and editors of its account may do so.
 */
class UpdateTag
{
    public function __construct(
        private readonly User $user,
        private readonly Tag $tag,
        private string $name,
    ) {}

    public function execute(): Tag
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->tag;
    }

    private function validate(): void
    {
        if (! $this->tag->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function update(): void
    {
        $this->tag->name = $this->name;
        $this->tag->updated_by_id = $this->user->id;
        $this->tag->updated_by_name = $this->user->getFullName();
        $this->tag->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TagUpdate,
            parameters: ['name' => $this->tag->name],
        )->onQueue('low');
    }
}
