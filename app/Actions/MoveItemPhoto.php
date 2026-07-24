<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\ItemPhoto;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Move a photo one step up or down by swapping its position with the
 * neighbouring photo of the same item. Only owners and editors of the item's
 * account may do so.
 */
class MoveItemPhoto
{
    public function __construct(
        private readonly User $user,
        private readonly ItemPhoto $itemPhoto,
        private string $direction = 'up',
    ) {}

    public function execute(): ItemPhoto
    {
        $this->validate();
        $this->swap();
        $this->log();

        return $this->itemPhoto;
    }

    private function validate(): void
    {
        if (! $this->itemPhoto->item->catalog->account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }
    }

    private function swap(): void
    {
        $photos = $this->itemPhoto->item->photos()->get();

        $index = $photos->search(fn (ItemPhoto $photo): bool => $photo->id === $this->itemPhoto->id);
        $target = $this->direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= $photos->count()) {
            return;
        }

        $neighbour = $photos[$target];

        DB::transaction(function () use ($neighbour): void {
            [$this->itemPhoto->position, $neighbour->position] = [$neighbour->position, $this->itemPhoto->position];
            $this->itemPhoto->save();
            $neighbour->save();
        });
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::ItemPhotoUpdate,
            parameters: ['name' => $this->itemPhoto->item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $this->itemPhoto->item,
            user: $this->user,
            action: ItemActionEnum::PhotoMoved,
        )->onQueue('low');
    }
}
