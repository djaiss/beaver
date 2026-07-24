<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateMaintenanceRecord;
use App\Actions\DestroyMaintenanceRecord;
use App\Actions\UpdateMaintenanceRecord;
use App\Enums\MaintenanceType;
use App\Http\Controllers\Controller;
use App\Models\Collection as CollectionModel;
use App\Models\Copy;
use App\Models\Item;
use App\Models\MaintenanceRecord;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The maintenance records of a copy, logged from the history tab of its item.
 *
 * The cost is typed in currency units on the form and stored in cents, so the
 * controller is the only place that knows about the conversion.
 */
class MaintenanceRecordController extends Controller
{
    public function create(Request $request, CollectionModel $collection, Item $item, Copy $copy): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        new CreateMaintenanceRecord(
            user: $request->user(),
            copy: $copy,
            type: MaintenanceType::from($validated['type']),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            performedBy: $validated['performed_by'] ?? null,
            performedAt: $validated['performed_at'] ?? null,
            costAmount: $this->toCents($validated['cost_amount'] ?? null),
            costCurrencyCode: $validated['currency'] ?? null,
            itemConditionBeforeId: $this->toId($validated['item_condition_before_id'] ?? null),
            itemConditionAfterId: $this->toId($validated['item_condition_after_id'] ?? null),
            nextDueAt: $validated['next_due_at'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return to_route('items.history.show', [$collection, $item, $copy, 'maintenance'])
            ->with('status', __('Maintenance record added'))
            ->with('status_description', __('The work was logged in the history of this copy.'));
    }

    public function update(Request $request, CollectionModel $collection, Item $item, Copy $copy, int $maintenanceRecord): RedirectResponse
    {
        $recordModel = $this->findRecord($copy, $maintenanceRecord);

        $validated = $request->validate($this->rules());

        new UpdateMaintenanceRecord(
            user: $request->user(),
            record: $recordModel,
            type: MaintenanceType::from($validated['type']),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            performedBy: $validated['performed_by'] ?? null,
            performedAt: $validated['performed_at'] ?? null,
            costAmount: $this->toCents($validated['cost_amount'] ?? null),
            costCurrencyCode: $validated['currency'] ?? null,
            itemConditionBeforeId: $this->toId($validated['item_condition_before_id'] ?? null),
            itemConditionAfterId: $this->toId($validated['item_condition_after_id'] ?? null),
            nextDueAt: $validated['next_due_at'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return to_route('items.history.show', [$collection, $item, $copy, 'maintenance'])
            ->with('status', __('Maintenance record updated'))
            ->with('status_description', __('Your changes to the work were saved.'));
    }

    public function destroy(Request $request, CollectionModel $collection, Item $item, Copy $copy, int $maintenanceRecord): RedirectResponse
    {
        $recordModel = $this->findRecord($copy, $maintenanceRecord);

        new DestroyMaintenanceRecord(
            user: $request->user(),
            record: $recordModel,
        )->execute();

        return to_route('items.history.show', [$collection, $item, $copy, 'maintenance'])
            ->with('status', __('Maintenance record deleted'))
            ->with('status_description', __('The work was removed from the history of this copy.'));
    }

    private function findRecord(Copy $copy, int $maintenanceRecord): MaintenanceRecord
    {
        try {
            return $copy->maintenanceRecords()->findOrFail($maintenanceRecord);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    /**
     * @return array<string, list<mixed>>
     */
    private function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(MaintenanceType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'performed_at' => ['nullable', 'date'],
            'cost_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'item_condition_before_id' => ['nullable', 'integer'],
            'item_condition_after_id' => ['nullable', 'integer'],
            'next_due_at' => ['nullable', 'date'],
            'include_in_provenance' => ['nullable', 'boolean'],
        ];
    }

    /**
     * The form collects the cost in currency units, and it is stored in cents.
     */
    private function toCents(mixed $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return (int) round((float) $amount * 100);
    }

    private function toId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
