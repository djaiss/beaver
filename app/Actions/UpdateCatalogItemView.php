<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemViewEnum;
use App\Models\Catalog;
use App\Models\CatalogView;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

/**
 * Remember which items view a user last opened for a collection. The preference is private to
 * the user, so any member of the collection's account may set their own.
 */
class UpdateCatalogItemView
{
    public function __construct(
        private readonly User $user,
        private readonly Catalog $catalog,
        private string $view,
    ) {}

    public function execute(): CatalogView
    {
        $this->validate();

        return $this->update();
    }

    private function validate(): void
    {
        if (! $this->userBelongsToAccount()) {
            throw new ModelNotFoundException('Account not found');
        }

        if (ItemViewEnum::tryFrom($this->view) === null) {
            throw ValidationException::withMessages(['view' => 'Invalid view']);
        }
    }

    private function userBelongsToAccount(): bool
    {
        return $this->catalog->account->roleFor($this->user) !== null;
    }

    private function update(): CatalogView
    {
        return CatalogView::updateOrCreate(
            ['user_id' => $this->user->id, 'catalog_id' => $this->catalog->id],
            ['items_view' => $this->view],
        );
    }
}
