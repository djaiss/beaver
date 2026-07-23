<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateLoan;
use App\Actions\DestroyLoan;
use App\Actions\ReturnLoan;
use App\Actions\UpdateLoan;
use App\Enums\LoanDirection;
use App\Enums\LoanStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\LoanResource;
use App\Models\Copy;
use App\Models\Loan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copy = $this->findCopy($request);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $loans = $copy->loans()->paginate($perPage);

        return LoanResource::collection($loans);
    }

    /**
     * Every loan in the account, across all copies, newest first. Optional
     * `direction` and `status` filters narrow the list. The web app's Loans
     * section reads the same data.
     */
    public function all(Request $request): AnonymousResourceCollection
    {
        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $query = Loan::query()
            ->forAccount($request->user()->account)
            ->with(['copy.item.collection', 'itemConditionOut', 'itemConditionIn', 'documents'])
            ->orderByDesc('loaned_at')
            ->orderByDesc('id');

        $direction = $request->query('direction');
        if (is_string($direction) && in_array($direction, array_column(LoanDirection::cases(), 'value'), true)) {
            $query->where('direction', $direction);
        }

        $status = $request->query('status');
        if (is_string($status) && in_array($status, array_column(LoanStatus::cases(), 'value'), true)) {
            $query->where('status', $status);
        }

        return LoanResource::collection($query->paginate($perPage));
    }

    public function show(Request $request): JsonResponse
    {
        $loan = $this->findLoan($request);

        return new LoanResource($loan)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copy = $this->findCopy($request);

        $validated = $this->validatePayload($request);

        $loan = new CreateLoan(
            user: $request->user(),
            copy: $copy,
            direction: LoanDirection::from($validated['direction']),
            party: $validated['party'],
            loanedAt: $validated['loaned_at'],
            status: LoanStatus::from($validated['status'] ?? LoanStatus::Active->value),
            purpose: $validated['purpose'] ?? null,
            dueAt: $validated['due_at'] ?? null,
            returnedAt: $validated['returned_at'] ?? null,
            itemConditionOutId: $validated['item_condition_out_id'] ?? null,
            itemConditionInId: $validated['item_condition_in_id'] ?? null,
            depositAmount: $validated['deposit_amount'] ?? null,
            depositCurrencyCode: $validated['deposit_currency_code'] ?? null,
            includeInProvenance: (bool) ($validated['include_in_provenance'] ?? false),
        )->execute();

        return new LoanResource($loan)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $loan = $this->findLoan($request);

        $validated = $this->validatePayload($request);

        $loan = new UpdateLoan(
            user: $request->user(),
            loan: $loan,
            direction: LoanDirection::from($validated['direction']),
            status: LoanStatus::from($validated['status'] ?? $loan->status->value),
            party: $validated['party'],
            loanedAt: $validated['loaned_at'],
            purpose: $validated['purpose'] ?? null,
            dueAt: $validated['due_at'] ?? null,
            returnedAt: $validated['returned_at'] ?? null,
            itemConditionOutId: $validated['item_condition_out_id'] ?? null,
            itemConditionInId: $validated['item_condition_in_id'] ?? null,
            depositAmount: $validated['deposit_amount'] ?? null,
            depositCurrencyCode: $validated['deposit_currency_code'] ?? null,
            includeInProvenance: (bool) ($validated['include_in_provenance'] ?? false),
        )->execute();

        return new LoanResource($loan)
            ->response()
            ->setStatusCode(200);
    }

    public function return(Request $request): JsonResponse
    {
        $loan = $this->findLoan($request);

        $validated = $request->validate([
            'returned_at' => ['required', 'date'],
            'item_condition_in_id' => ['nullable', 'integer'],
        ]);

        $loan = new ReturnLoan(
            user: $request->user(),
            loan: $loan,
            returnedAt: $validated['returned_at'],
            itemConditionInId: $validated['item_condition_in_id'] ?? null,
        )->execute();

        return new LoanResource($loan)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $loan = $this->findLoan($request);

        new DestroyLoan(
            user: $request->user(),
            loan: $loan,
        )->execute();

        return response()->noContent(204);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'direction' => ['required', Rule::enum(LoanDirection::class)],
            'status' => ['nullable', Rule::enum(LoanStatus::class)],
            'party' => ['required', 'string', 'max:255'],
            'purpose' => ['nullable', 'string'],
            'loaned_at' => ['required', 'date'],
            'due_at' => ['nullable', 'date'],
            'returned_at' => ['nullable', 'date'],
            'item_condition_out_id' => ['nullable', 'integer'],
            'item_condition_in_id' => ['nullable', 'integer'],
            'deposit_amount' => ['nullable', 'integer', 'min:0'],
            'deposit_currency_code' => ['nullable', 'string', 'size:3'],
            'include_in_provenance' => ['nullable', 'boolean'],
        ]);
    }

    private function findCopy(Request $request): Copy
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;

        return Copy::query()
            ->whereHas('item.collection', fn ($query) => $query->whereBelongsTo($account))
            ->findOrFail($copyId);
    }

    private function findLoan(Request $request): Loan
    {
        $copy = $this->findCopy($request);
        $loanId = $request->route()->parameter('loan');

        return $copy->loans()->findOrFail($loanId);
    }
}
