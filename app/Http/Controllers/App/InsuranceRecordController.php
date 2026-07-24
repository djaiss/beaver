<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateInsuranceRecord;
use App\Actions\DestroyInsuranceRecord;
use App\Actions\UpdateInsuranceRecord;
use App\Enums\InsuranceStatus;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\InsuranceRecord;
use App\Models\Item;
use App\Traits\FindsItems;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The insurance records of a copy, recorded from the history tab of its item.
 *
 * The insured value and the deductible are typed in currency units on the form
 * and stored in cents, so the controller is the only place that knows about the
 * conversion.
 */
class InsuranceRecordController extends Controller
{
    use FindsItems;

    public function create(Request $request, int $collection, int $item, int $copy): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);

        $validated = $request->validate($this->rules());

        new CreateInsuranceRecord(
            user: $request->user(),
            copy: $copyModel,
            provider: $validated['provider'],
            insuredValue: (int) round((float) $validated['insured_value'] * 100),
            status: InsuranceStatus::from($validated['status']),
            currencyCode: $validated['currency'] ?? null,
            policyNumber: $validated['policy_number'] ?? null,
            coverageType: $validated['coverage_type'] ?? null,
            deductibleAmount: $this->toCents($validated['deductible_amount'] ?? null),
            startsAt: $validated['starts_at'] ?? null,
            endsAt: $validated['ends_at'] ?? null,
            isScheduledItem: $request->boolean('is_scheduled_item'),
            contactName: $validated['contact_name'] ?? null,
            contactEmail: $validated['contact_email'] ?? null,
            contactPhone: $validated['contact_phone'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'insurance'])
            ->with('status', __('Insurance record added'))
            ->with('status_description', __('The coverage was added to the history of this copy.'));
    }

    public function update(Request $request, int $collection, int $item, int $copy, int $insuranceRecord): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $recordModel = $this->findRecord($copyModel, $insuranceRecord);

        $validated = $request->validate($this->rules());

        new UpdateInsuranceRecord(
            user: $request->user(),
            record: $recordModel,
            provider: $validated['provider'],
            insuredValue: (int) round((float) $validated['insured_value'] * 100),
            status: InsuranceStatus::from($validated['status']),
            currencyCode: $validated['currency'] ?? null,
            policyNumber: $validated['policy_number'] ?? null,
            coverageType: $validated['coverage_type'] ?? null,
            deductibleAmount: $this->toCents($validated['deductible_amount'] ?? null),
            startsAt: $validated['starts_at'] ?? null,
            endsAt: $validated['ends_at'] ?? null,
            isScheduledItem: $request->boolean('is_scheduled_item'),
            contactName: $validated['contact_name'] ?? null,
            contactEmail: $validated['contact_email'] ?? null,
            contactPhone: $validated['contact_phone'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'insurance'])
            ->with('status', __('Insurance record updated'))
            ->with('status_description', __('Your changes to the coverage were saved.'));
    }

    public function destroy(Request $request, int $collection, int $item, int $copy, int $insuranceRecord): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $recordModel = $this->findRecord($copyModel, $insuranceRecord);

        new DestroyInsuranceRecord(
            user: $request->user(),
            record: $recordModel,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'insurance'])
            ->with('status', __('Insurance record deleted'))
            ->with('status_description', __('The coverage was removed from the history of this copy.'));
    }

    private function findCopy(Item $item, int $copy): Copy
    {
        try {
            return $item->copies()->findOrFail($copy);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findRecord(Copy $copy, int $insuranceRecord): InsuranceRecord
    {
        try {
            return $copy->insuranceRecords()->findOrFail($insuranceRecord);
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
            'provider' => ['required', 'string', 'max:255'],
            'insured_value' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::enum(InsuranceStatus::class)],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'policy_number' => ['nullable', 'string', 'max:255'],
            'coverage_type' => ['nullable', 'string', 'max:255'],
            'deductible_amount' => ['nullable', 'numeric', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_scheduled_item' => ['nullable', 'boolean'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * The form collects money in currency units, and everything is stored in
     * cents.
     */
    private function toCents(mixed $amount): ?int
    {
        if ($amount === null || $amount === '') {
            return null;
        }

        return (int) round((float) $amount * 100);
    }
}
