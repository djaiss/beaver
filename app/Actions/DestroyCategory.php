<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Delete a category, cascading to its nested child categories. Only owners
 * and editors of its collection's account may do so.
 */
class DestroyCategory
{
    public function __construct(
        private readonly User $user,
        private readonly Category $category,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->deleteDescendants($this->category);
        $this->category->delete();
    }

    private function validate(): void
    {
        if (! $this->category->catalog->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Category not found');
        }
    }

    private function deleteDescendants(Category $category): void
    {
        foreach ($category->children as $child) {
            $this->deleteDescendants($child);
            $child->delete();
        }
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CategoryDeletion,
            parameters: ['name' => $this->category->name],
        )->onQueue('low');
    }
}
