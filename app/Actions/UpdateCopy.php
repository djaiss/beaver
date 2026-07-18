<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\UserActionEnum;
use App\Jobs\LogUserAction;
use App\Models\Condition;
use App\Models\Copy;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a copy of an item. Only owners and editors of its account may do so.
 */
class UpdateCopy
{
    public function __construct(
        private readonly User $user,
        private readonly Copy $copy,
        private readonly ?Condition $condition = null,
        private readonly ?Location $location = null,
        private readonly ?string $acquiredAt = null,
        private readonly ?int $pricePaid = null,
        private readonly ?int $estimatedValue = null,
    ) {}

    public function execute(): Copy
    {
        $this->validate();
        $this->update();
        $this->log();

        return $this->copy;
    }

    private function validate(): void
    {
        $account = $this->copy->item->collection->account;

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

    private function update(): void
    {
        $this->copy->condition_id = $this->condition?->id;
        $this->copy->location_id = $this->location?->id;
        $this->copy->acquired_at = $this->acquiredAt;
        $this->copy->price_paid = $this->pricePaid;
        $this->copy->estimated_value = $this->estimatedValue;
        $this->copy->updated_by_id = $this->user->id;
        $this->copy->updated_by_name = $this->user->getFullName();
        $this->copy->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::CopyUpdate,
            parameters: ['name' => $this->copy->item->name],
        )->onQueue('low');
    }
}
