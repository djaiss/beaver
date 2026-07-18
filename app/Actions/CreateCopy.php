<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Create a copy of an item. Only owners and editors of the item's account may
 * do so.
 */
class CreateCopy
{
    private Copy $copy;

    public function __construct(
        private readonly User $user,
        private readonly Item $item,
        private readonly ?Condition $condition = null,
        private readonly ?Location $location = null,
        private readonly ?string $acquiredAt = null,
        private readonly ?int $pricePaid = null,
        private readonly ?int $estimatedValue = null,
    ) {}

    public function execute(): Copy
    {
        $this->validate();
        $this->create();
        $this->stampAuthor();
        $this->log();

        return $this->copy;
    }

    private function validate(): void
    {
        $account = $this->item->collection->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        if ($this->condition instanceof Condition && $this->condition->account_id !== $account->id) {
            throw new ModelNotFoundException('Condition not found');
        }

        if ($this->location instanceof Location && $this->location->account_id !== $account->id) {
            throw new ModelNotFoundException('Location not found');
        }
    }

    private function create(): void
    {
        $this->copy = Copy::query()->create([
            'item_id' => $this->item->id,
            'condition_id' => $this->condition?->id,
            'location_id' => $this->location?->id,
            'acquired_at' => $this->acquiredAt,
            'price_paid' => $this->pricePaid,
            'estimated_value' => $this->estimatedValue,
        ]);
    }

    private function stampAuthor(): void
    {
        $this->copy->created_by_id = $this->user->id;
        $this->copy->created_by_name = $this->user->getFullName();
        $this->copy->updated_by_id = $this->user->id;
        $this->copy->updated_by_name = $this->user->getFullName();
        $this->copy->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CopyCreation,
            parameters: ['name' => $this->item->name],
        )->onQueue('low');
    }
}
