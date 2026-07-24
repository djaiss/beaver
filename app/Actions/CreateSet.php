<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Set;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a set within a collection. Only owners and editors may do so.
 */
class CreateSet
{
    private Set $set;

    public function __construct(
        private readonly User $user,
        private readonly Catalog $catalog,
        private string $name,
        private ?string $description = null,
        private readonly ?int $targetCount = null,
    ) {}

    public function execute(): Set
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->set;
    }

    private function validate(): void
    {
        if (! $this->catalog->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Catalog not found');
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);
        $this->description = TextSanitizer::nullablePlainText($this->description);
    }

    private function create(): void
    {
        $this->set = Set::query()->create([
            'catalog_id' => $this->catalog->id,
            'name' => $this->name,
            'description' => $this->description,
            'target_count' => $this->targetCount,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->set->created_by_id = $this->user->id;
        $this->set->created_by_name = $this->user->getFullName();
        $this->set->updated_by_id = $this->user->id;
        $this->set->updated_by_name = $this->user->getFullName();
        $this->set->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::SetCreation,
            parameters: ['name' => $this->set->name],
        )->onQueue('low');
    }
}
