<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ItemActionEnum;
use App\Enums\UserActionEnum;
use App\Helpers\Money;
use App\Jobs\LogItemAction;
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
    /**
     * The values that moved, captured before the copy is written so the
     * activity tab can show what they moved from.
     *
     * @var list<array{label: string, from: string|null, to: string|null}>
     */
    private array $changes = [];

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
        $this->captureChanges();
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

    /**
     * Read what is about to move, while the copy still holds its old values.
     */
    private function captureChanges(): void
    {
        $currency = $this->copy->item->collection->currency;

        $this->changes = array_values(array_filter([
            $this->change('Condition', $this->copy->condition?->name, $this->condition?->name),
            $this->change('Location', $this->copy->location?->name, $this->location?->name),
            $this->change('Acquired', $this->copy->acquired_at?->toDateString(), $this->acquiredAt),
            $this->change('Price paid', $this->amount($this->copy->price_paid, $currency), $this->amount($this->pricePaid, $currency)),
            $this->change('Estimated value', $this->amount($this->copy->estimated_value, $currency), $this->amount($this->estimatedValue, $currency)),
        ]));
    }

    /**
     * @return array{label: string, from: string|null, to: string|null}|null
     */
    private function change(string $label, ?string $from, ?string $to): ?array
    {
        if ($from === $to) {
            return null;
        }

        return ['label' => $label, 'from' => $from, 'to' => $to];
    }

    private function amount(?int $cents, ?string $currency): ?string
    {
        return $cents === null ? null : Money::format($cents, $currency);
    }

    private function update(): void
    {
        $this->copy->fill([
            'condition_id' => $this->condition?->id,
            'location_id' => $this->location?->id,
            'acquired_at' => $this->acquiredAt,
            'price_paid' => $this->pricePaid,
            'estimated_value' => $this->estimatedValue,
        ]);
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

        LogItemAction::dispatch(
            item: $this->copy->item,
            user: $this->user,
            action: ItemActionEnum::CopyUpdate,
            parameters: $this->changes === [] ? null : ['changes' => $this->changes],
        )->onQueue('low');
    }
}
