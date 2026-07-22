<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\ReturnLoan;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Marking a loan as returned, its own flow rather than an edit of the loan.
 *
 * Closing a loan is a distinct step: it captures the date the copy came back and
 * the condition it came back in, brings the copy back into custody and, when the
 * loan is part of provenance, records the return. It lives in its own controller
 * so the loan controller stays a plain resource.
 */
class LoanReturnController extends Controller
{
    use FindsItems;

    public function update(Request $request, int $collection, int $item, int $copy, int $loan): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $loanModel = $this->findLoan($copyModel, $loan);

        $validated = $request->validate([
            'returned_at' => ['required', 'date'],
            'item_condition_in_id' => ['nullable', 'integer'],
        ]);

        new ReturnLoan(
            user: $request->user(),
            loan: $loanModel,
            returnedAt: $validated['returned_at'],
            itemConditionInId: $this->toId($validated['item_condition_in_id'] ?? null),
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'loans'])
            ->with('status', __('Loan marked as returned'))
            ->with('status_description', __('The copy is back in your custody.'));
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

    private function toId(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
