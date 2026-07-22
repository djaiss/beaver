<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Account;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a tag within an account. Only owners and editors may do so.
 */
class CreateTag
{
    private Tag $tag;

    public function __construct(
        private readonly User $user,
        private readonly Account $account,
        private string $name,
    ) {}

    public function execute(): Tag
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->tag;
    }

    private function validate(): void
    {
        if (! $this->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
    }

    private function create(): void
    {
        $this->tag = Tag::query()->create([
            'account_id' => $this->account->id,
            'name' => $this->name,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->tag->created_by_id = $this->user->id;
        $this->tag->created_by_name = $this->user->getFullName();
        $this->tag->updated_by_id = $this->user->id;
        $this->tag->updated_by_name = $this->user->getFullName();
        $this->tag->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::TagCreation,
            parameters: ['name' => $this->tag->name],
        )->onQueue('low');
    }
}
