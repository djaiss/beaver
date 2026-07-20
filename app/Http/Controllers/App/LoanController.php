<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateLoan;
use App\Actions\DestroyLoan;
use App\Actions\UpdateLoan;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The loans of a copy, logged from the history tab of its item.
 *
 * A loan moves custody without moving ownership. The deposit is typed in
 * currency units on the form and stored in cents, so the controller is the only
 * place that knows about the conversion. The return is its own flow rather than
 * an edit, so the date and the condition on return are captured on their own.
 */
class LoanController extends Controller
{
    use FindsItems;

    public function create(Request $request, int $collection, int $item, int $copy): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);

        $validated = $request->validate($this->rules());

        new CreateLoan(
            user: $request->user(),
            copy: $copyModel,
            direction: LoanDirection::from($validated['direction']),
            party: $validated['party'],
            loanedAt: $validated['loaned_at'],
            status: LoanStatus::from($validated['status']),
            purpose: $validated['purpose'] ?? null,
            dueAt: $validated['due_at'] ?? null,
            returnedAt: $validated['returned_at'] ?? null,
            conditionOutId: $this->toId($validated['condition_out_id'] ?? null),
            conditionInId: $this->toId($validated['condition_in_id'] ?? null),
            depositAmount: $this->toCents($validated['deposit_amount'] ?? null),
            depositCurrencyCode: $validated['currency'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'loans'])
            ->with('status', __('Loan recorded'))
            ->with('status_description', __('The loan was logged in the history of this copy.'));
    }

    public function update(Request $request, int $collection, int $item, int $copy, int $loan): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $loanModel = $this->findLoan($copyModel, $loan);

        $validated = $request->validate($this->rules());

        new UpdateLoan(
            user: $request->user(),
            loan: $loanModel,
            direction: LoanDirection::from($validated['direction']),
            status: LoanStatus::from($validated['status']),
            party: $validated['party'],
            loanedAt: $validated['loaned_at'],
            purpose: $validated['purpose'] ?? null,
            dueAt: $validated['due_at'] ?? null,
            returnedAt: $validated['returned_at'] ?? null,
            conditionOutId: $this->toId($validated['condition_out_id'] ?? null),
            conditionInId: $this->toId($validated['condition_in_id'] ?? null),
            depositAmount: $this->toCents($validated['deposit_amount'] ?? null),
            depositCurrencyCode: $validated['currency'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'loans'])
            ->with('status', __('Loan updated'))
            ->with('status_description', __('Your changes to the loan were saved.'));
    }

    public function destroy(Request $request, int $collection, int $item, int $copy, int $loan): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $loanModel = $this->findLoan($copyModel, $loan);

        new DestroyLoan(
            user: $request->user(),
            loan: $loanModel,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'loans'])
            ->with('status', __('Loan deleted'))
            ->with('status_description', __('The loan was removed from the history of this copy.'));
    }

    private function findCopy(Item $item, int $copy): Copy
    {
        try {
            return $item->copies()->findOrFail($copy);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findLoan(Copy $copy, int $loan): Loan
    {
        try {
            return $copy->loans()->findOrFail($loan);
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
            'direction' => ['required', Rule::enum(LoanDirection::class)],
            'status' => ['required', Rule::enum(LoanStatus::class)],
            'party' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:2000'],
            'loaned_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date'],
            'returned_at' => ['nullable', 'date'],
            'condition_out_id' => ['nullable', 'integer'],
            'condition_in_id' => ['nullable', 'integer'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'include_in_provenance' => ['nullable', 'boolean'],
        ];
    }

    /**
     * The form collects the deposit in currency units, and it is stored in cents.
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
