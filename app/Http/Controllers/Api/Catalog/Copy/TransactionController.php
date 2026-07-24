<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog\Copy;

use App\Actions\CreateTransaction;
use App\Actions\DestroyTransaction;
use App\Actions\UpdateTransaction;
use App\Enums\TransactionType;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $transactions = $copy->transactions()->paginate($perPage);

        return TransactionResource::collection($transactions);
    }

    public function show(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $transactionId = $request->route()->parameter('transaction');
        $transaction = $copy->transactions()->findOrFail($transactionId);

        return new TransactionResource($transaction)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'occurred_at' => ['required', 'date'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'fee_amount' => ['nullable', 'integer', 'min:0'],
            'shipping_amount' => ['nullable', 'integer', 'min:0'],
            'total_amount' => ['nullable', 'integer', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            'note' => ['nullable', 'string'],
        ]);

        $transaction = new CreateTransaction(
            user: $request->user(),
            copy: $copy,
            type: TransactionType::from($validated['type']),
            occurredAt: $validated['occurred_at'],
            counterparty: $validated['counterparty'] ?? null,
            amount: $validated['amount'] ?? null,
            currencyCode: $validated['currency_code'] ?? null,
            taxAmount: $validated['tax_amount'] ?? null,
            feeAmount: $validated['fee_amount'] ?? null,
            shippingAmount: $validated['shipping_amount'] ?? null,
            totalAmount: $validated['total_amount'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new TransactionResource($transaction)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $transactionId = $request->route()->parameter('transaction');
        $transaction = $copy->transactions()->findOrFail($transactionId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(TransactionType::class)],
            'occurred_at' => ['required', 'date'],
            'counterparty' => ['nullable', 'string', 'max:255'],
            'amount' => ['nullable', 'integer', 'min:0'],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'tax_amount' => ['nullable', 'integer', 'min:0'],
            'fee_amount' => ['nullable', 'integer', 'min:0'],
            'shipping_amount' => ['nullable', 'integer', 'min:0'],
            'total_amount' => ['nullable', 'integer', 'min:0'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'source_url' => ['nullable', 'string', 'url', 'max:2048'],
            'note' => ['nullable', 'string'],
        ]);

        $transaction = new UpdateTransaction(
            user: $request->user(),
            transaction: $transaction,
            type: TransactionType::from($validated['type']),
            occurredAt: $validated['occurred_at'],
            counterparty: $validated['counterparty'] ?? null,
            amount: $validated['amount'] ?? null,
            currencyCode: $validated['currency_code'] ?? null,
            taxAmount: $validated['tax_amount'] ?? null,
            feeAmount: $validated['fee_amount'] ?? null,
            shippingAmount: $validated['shipping_amount'] ?? null,
            totalAmount: $validated['total_amount'] ?? null,
            referenceNumber: $validated['reference_number'] ?? null,
            sourceUrl: $validated['source_url'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new TransactionResource($transaction)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $transactionId = $request->route()->parameter('transaction');
        $transaction = $copy->transactions()->findOrFail($transactionId);

        new DestroyTransaction(
            user: $request->user(),
            transaction: $transaction,
        )->execute();

        return response()->noContent(204);
    }
}
