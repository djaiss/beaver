<?php

declare(strict_types=1);

namespace App\Http\Controllers\App;

use App\Actions\CreateTransaction;
use App\Actions\DestroyTransaction;
use App\Actions\UpdateTransaction;
use App\Enums\TransactionType;
use App\Http\Controllers\Concerns\FindsItems;
use App\Http\Controllers\Controller;
use App\Models\Copy;
use App\Models\Item;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The transactions of a copy, recorded from the history tab of its item.
 *
 * Every amount is typed in currency units on the form and stored in cents, so
 * the controller is the only place that knows about the conversion.
 */
class TransactionController extends Controller
{
    use FindsItems;

    public function create(Request $request, int $collection, int $item, int $copy): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);

        $validated = $request->validate($this->rules());

        new CreateTransaction(
            user: $request->user(),
            copy: $copyModel,
            type: TransactionType::from($validated['type']),
            occurredAt: $validated['occurred_at'],
            counterparty: $validated['counterparty'] ?? null,
            amount: $this->toCents($validated['amount'] ?? null),
            currencyCode: $validated['currency'] ?? null,
            taxAmount: $this->toCents($validated['tax_amount'] ?? null),
            feeAmount: $this->toCents($validated['fee_amount'] ?? null),
            shippingAmount: $this->toCents($validated['shipping_amount'] ?? null),
            totalAmount: $this->toCents($validated['total_amount'] ?? null),
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'transactions'])
            ->with('status', __('Transaction recorded'))
            ->with('status_description', __('The transaction was added to the history of this copy.'));
    }

    public function update(Request $request, int $collection, int $item, int $copy, int $transaction): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $transactionModel = $this->findTransaction($copyModel, $transaction);

        $validated = $request->validate($this->rules());

        new UpdateTransaction(
            user: $request->user(),
            transaction: $transactionModel,
            type: TransactionType::from($validated['type']),
            occurredAt: $validated['occurred_at'],
            counterparty: $validated['counterparty'] ?? null,
            amount: $this->toCents($validated['amount'] ?? null),
            currencyCode: $validated['currency'] ?? null,
            taxAmount: $this->toCents($validated['tax_amount'] ?? null),
            feeAmount: $this->toCents($validated['fee_amount'] ?? null),
            shippingAmount: $this->toCents($validated['shipping_amount'] ?? null),
            totalAmount: $this->toCents($validated['total_amount'] ?? null),
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'transactions'])
            ->with('status', __('Transaction updated'))
            ->with('status_description', __('Your changes to the transaction were saved.'));
    }

    public function destroy(Request $request, int $collection, int $item, int $copy, int $transaction): RedirectResponse
    {
        $collectionModel = $this->findCollection($request, $collection);
        $itemModel = $this->findItem($collectionModel, $item, []);
        $copyModel = $this->findCopy($itemModel, $copy);
        $transactionModel = $this->findTransaction($copyModel, $transaction);

        new DestroyTransaction(
            user: $request->user(),
            transaction: $transactionModel,
        )->execute();

        return to_route('items.history.show', [$collectionModel, $itemModel, $copyModel, 'section' => 'transactions'])
            ->with('status', __('Transaction deleted'))
            ->with('status_description', __('The transaction was removed from the history of this copy.'));
    }

    private function findCopy(Item $item, int $copy): Copy
    {
        try {
            return $item->copies()->findOrFail($copy);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }

    private function findTransaction(Copy $copy, int $transaction): Transaction
    {
        try {
            return $copy->transactions()->findOrFail($transaction);
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
            'type' => ['required', Rule::enum(TransactionType::class)],
            'occurred_at' => ['required', 'date'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', Rule::in(array_keys(config('currencies')))],
            'tax_amount' => ['nullable', 'numeric', 'min:0'],
            'fee_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'url', 'max:2000'],
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
