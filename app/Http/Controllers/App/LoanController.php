<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateLoan;
use App\Actions\DestroyLoan;
use App\Actions\UpdateLoan;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
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
    public function create(Request $request, Catalog $catalog, Item $item, Copy $copy): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $loan = new CreateLoan(
            user: $request->user(),
            copy: $copy,
            direction: LoanDirection::from($validated['direction']),
            party: $validated['party'],
            loanedAt: $validated['loaned_at'],
            status: LoanStatus::from($validated['status']),
            purpose: $validated['purpose'] ?? null,
            dueAt: $validated['due_at'] ?? null,
            returnedAt: $validated['returned_at'] ?? null,
            itemConditionOutId: $this->toId($validated['item_condition_out_id'] ?? null),
            itemConditionInId: $this->toId($validated['item_condition_in_id'] ?? null),
            depositAmount: $this->toCents($validated['deposit_amount'] ?? null),
            depositCurrencyCode: $validated['currency'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return $this->redirectToLoansSection($request, $loan, __('Loan recorded'), __('The loan was logged in the history of this copy.'))
            ?? to_route('items.history.show', [$catalog, $item, $copy, 'loans'])
                ->with('status', __('Loan recorded'))
                ->with('status_description', __('The loan was logged in the history of this copy.'));
    }

    public function update(Request $request, Catalog $catalog, Item $item, Copy $copy, int $loan): RedirectResponse
    {
        $loanModel = $this->findLoan($copy, $loan);

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
            itemConditionOutId: $this->toId($validated['item_condition_out_id'] ?? null),
            itemConditionInId: $this->toId($validated['item_condition_in_id'] ?? null),
            depositAmount: $this->toCents($validated['deposit_amount'] ?? null),
            depositCurrencyCode: $validated['currency'] ?? null,
            includeInProvenance: $request->boolean('include_in_provenance'),
        )->execute();

        return $this->redirectToLoansSection($request, $loanModel->refresh(), __('Loan updated'), __('Your changes to the loan were saved.'))
            ?? to_route('items.history.show', [$catalog, $item, $copy, 'loans'])
                ->with('status', __('Loan updated'))
                ->with('status_description', __('Your changes to the loan were saved.'));
    }

    public function destroy(Request $request, Catalog $catalog, Item $item, Copy $copy, int $loan): RedirectResponse
    {
        $loanModel = $this->findLoan($copy, $loan);

        $direction = $loanModel->direction;

        new DestroyLoan(
            user: $request->user(),
            loan: $loanModel,
        )->execute();

        if ($request->input('from') === 'loans') {
            return to_route('loans.show', ['direction' => $direction->slug(), 'tab' => 'all'])
                ->with('status', __('Loan deleted'))
                ->with('status_description', __('The loan was removed from the history of this copy.'));
        }

        return to_route('items.history.show', [$catalog, $item, $copy, 'loans'])
            ->with('status', __('Loan deleted'))
            ->with('status_description', __('The loan was removed from the history of this copy.'));
    }

    /**
     * When a loan form is submitted from the Loans section rather than the copy's
     * history tab, send the user back to that loan's drawer instead of the item.
     */
    private function redirectToLoansSection(Request $request, Loan $loan, string $status, string $description): ?RedirectResponse
    {
        if ($request->input('from') !== 'loans') {
            return null;
        }

        return to_route('loans.show', ['direction' => $loan->direction->slug(), 'tab' => 'all', 'loan' => $loan->id])
            ->with('status', $status)
            ->with('status_description', $description);
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
            'item_condition_out_id' => ['nullable', 'integer'],
            'item_condition_in_id' => ['nullable', 'integer'],
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
