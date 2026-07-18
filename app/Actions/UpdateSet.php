<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Set;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a set's name and description. Only owners and editors of its
 * account may do so.
 */
class UpdateSet
{
    public function __construct(
        private readonly User $user,
        private readonly Set $set,
        private string $name,
        private ?string $description = null,
    ) {}

    public function execute(): Set
    {
        $this->validate();
        $this->sanitize();
        $this->update();
        $this->log();

        return $this->set;
    }

    private function validate(): void
    {
        if (! $this->set->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function update(): void
    {
        $this->set->name = $this->name;
        $this->set->description = $this->description;
        $this->set->updated_by_id = $this->user->id;
        $this->set->updated_by_name = $this->user->getFullName();
        $this->set->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SetUpdate,
            parameters: ['name' => $this->set->name],
        )->onQueue('low');
    }
}
