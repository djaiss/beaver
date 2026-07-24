<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Models\Catalog;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Create a category within a collection. Only owners and editors of the
 * collection's account may do so.
 */
class CreateCategory
{
    private Category $category;

    public function __construct(
        private readonly User $user,
        private readonly Catalog $catalog,
        private string $name,
        private ?int $parentId = null,
        private ?string $description = null,
    ) {}

    public function execute(): Category
    {
        $this->validate();
        $this->sanitize();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->category;
    }

    private function validate(): void
    {
        if (! $this->catalog->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Catalog not found');
        }

        if ($this->parentId !== null && ! $this->catalog->categories()->whereKey($this->parentId)->exists()) {
            throw ValidationException::withMessages(['parent_id' => 'Invalid parent category']);
        }
    }

    private function sanitize(): void
    {
        $this->name = TextSanitizer::plainText($this->name);

        if ($this->description !== null) {
            $this->description = TextSanitizer::plainText($this->description);
        }
    }

    private function create(): void
    {
        $this->category = Category::query()->create([
            'catalog_id' => $this->catalog->id,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'description' => $this->description,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->category->created_by_id = $this->user->id;
        $this->category->created_by_name = $this->user->getFullName();
        $this->category->updated_by_id = $this->user->id;
        $this->category->updated_by_name = $this->user->getFullName();
        $this->category->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CategoryCreation,
            parameters: ['name' => $this->category->name],
        )->onQueue('low');
    }
}
