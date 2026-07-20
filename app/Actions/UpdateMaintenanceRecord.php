<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\DatePrecision;
use App\Enums\ItemActionEnum;
use App\Enums\MaintenanceType;
use App\Enums\ProvenanceEventType;
use App\Enums\UserActionEnum;
use App\Helpers\Money;
use App\Jobs\LogItemAction;
use App\Jobs\LogUserAction;
use App\Models\MaintenanceRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Update a piece of work performed on a copy. Only owners and editors of its
 * account may do so.
 *
 * Turning the provenance flag on generates the matching event, and turning it
 * off removes it, so the object's story stays in step with what the record now
 * claims to be.
 */
class UpdateMaintenanceRecord
{
    use GuardsMaintenanceConditions;

    /**
     * The values that moved, captured before the record is written so the
     * activity tab can show what they moved from.
     *
     * @var list<array{label: string, from: string|null, to: string|null}>
     */
    private array $changes = [];

    public function __construct(
        private readonly User $user,
        private readonly MaintenanceRecord $record,
        private readonly MaintenanceType $type,
        private readonly string $title,
        private readonly ?string $description = null,
        private readonly ?string $performedBy = null,
        private readonly ?string $performedAt = null,
        private readonly ?int $costAmount = null,
        private readonly ?string $costCurrencyCode = null,
        private readonly ?int $conditionBeforeId = null,
        private readonly ?int $conditionAfterId = null,
        private readonly ?string $nextDueAt = null,
        private readonly bool $includeInProvenance = false,
    ) {}

    public function execute(): MaintenanceRecord
    {
        $this->validate();
        $this->captureChanges();
        $this->update();
        $this->syncCopyCondition();
        $this->reconcileProvenance();
        $this->log();

        return $this->record;
    }

    private function validate(): void
    {
        $account = $this->record->copy->item->collection->account;

        if (! $account->allowsManagementBy($this->user)) {
            throw new ModelNotFoundException('Account not found');
        }

        $this->guardConditionsBelongToAccount($account, $this->conditionBeforeId, $this->conditionAfterId);
    }

    /**
     * Read what is about to move, while the record still holds its old values.
     */
    private function captureChanges(): void
    {
        $currency = $this->record->cost_currency_code;

        $this->changes = array_values(array_filter([
            $this->change('Type', $this->record->type->label(), $this->type->label()),
            $this->change('Title', $this->record->title, $this->title),
            $this->change(
                'Cost',
                $this->record->cost_amount === null ? null : Money::format($this->record->cost_amount, $currency),
                $this->costAmount === null ? null : Money::format($this->costAmount, $this->costCurrencyCode ?? $currency),
            ),
            $this->change('Performed', $this->record->performed_at?->toDateString(), $this->performedAt),
            $this->change('Next due', $this->record->next_due_at?->toDateString(), $this->nextDueAt),
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

    private function update(): void
    {
        $this->record->fill([
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'performed_by' => $this->performedBy,
            'performed_at' => $this->performedAt,
            'cost_amount' => $this->costAmount,
            'cost_currency_code' => $this->costAmount === null
                ? null
                : ($this->costCurrencyCode ?? $this->record->cost_currency_code ?? $this->record->copy->item->collection->currency),
            'condition_before_id' => $this->conditionBeforeId,
            'condition_after_id' => $this->conditionAfterId,
            'next_due_at' => $this->nextDueAt,
            'include_in_provenance' => $this->includeInProvenance,
        ]);
        $this->record->updated_by_id = $this->user->id;
        $this->record->updated_by_name = $this->user->getFullName();
        $this->record->save();
    }

    /**
     * Work changes the object, so its condition afterwards becomes the copy's
     * current condition.
     */
    private function syncCopyCondition(): void
    {
        if ($this->conditionAfterId === null) {
            return;
        }

        $copy = $this->record->copy;
        $copy->condition_id = $this->conditionAfterId;
        $copy->save();
    }

    /**
     * Keep the linked provenance event in step with the flag: create it when the
     * record is newly marked for provenance, remove it when the mark is taken off.
     */
    private function reconcileProvenance(): void
    {
        if ($this->includeInProvenance && $this->record->provenance_event_id === null) {
            $event = new CreateProvenanceEvent(
                user: $this->user,
                copy: $this->record->copy,
                type: ProvenanceEventType::SignificantRestoration,
                title: $this->title,
                occurredAtPrecision: $this->performedAt === null ? DatePrecision::Unknown : DatePrecision::Exact,
                occurredAt: $this->performedAt,
                description: $this->description,
            )->execute();

            $this->record->provenance_event_id = $event->id;
            $this->record->save();

            return;
        }

        if (! $this->includeInProvenance && $this->record->provenance_event_id !== null) {
            $this->record->provenanceEvent?->delete();
            $this->record->provenance_event_id = null;
            $this->record->save();
        }
    }

    private function log(): void
    {
        $item = $this->record->copy->item;

        LogUserAction::dispatch(
            user: $this->user,
            action: UserActionEnum::MaintenanceRecordUpdate,
            parameters: ['name' => $item->name],
        )->onQueue('low');

        LogItemAction::dispatch(
            item: $item,
            user: $this->user,
            action: ItemActionEnum::MaintenanceRecordUpdate,
            parameters: $this->changes === [] ? null : ['changes' => $this->changes],
        )->onQueue('low');
    }
}
