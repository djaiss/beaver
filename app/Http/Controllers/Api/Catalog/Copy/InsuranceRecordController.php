<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Catalog\Copy;

use App\Actions\CreateInsuranceRecord;
use App\Actions\DestroyInsuranceRecord;
use App\Actions\UpdateInsuranceRecord;
use App\Enums\InsuranceStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\InsuranceRecordResource;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class InsuranceRecordController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $records = $copy->insuranceRecords()->paginate($perPage);

        return InsuranceRecordResource::collection($records);
    }

    public function show(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('insuranceRecord');
        $record = $copy->insuranceRecords()->findOrFail($recordId);

        return new InsuranceRecordResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'insured_value' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', Rule::enum(InsuranceStatus::class)],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'policy_number' => ['nullable', 'string', 'max:255'],
            'coverage_type' => ['nullable', 'string', 'max:255'],
            'deductible_amount' => ['nullable', 'integer', 'min:0'],
            'deductible_currency_code' => ['nullable', 'string', 'size:3'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_scheduled_item' => ['nullable', 'boolean'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $record = new CreateInsuranceRecord(
            user: $request->user(),
            copy: $copy,
            provider: $validated['provider'],
            insuredValue: $validated['insured_value'],
            status: InsuranceStatus::from($validated['status'] ?? InsuranceStatus::Active->value),
            currencyCode: $validated['currency_code'] ?? null,
            policyNumber: $validated['policy_number'] ?? null,
            coverageType: $validated['coverage_type'] ?? null,
            deductibleAmount: $validated['deductible_amount'] ?? null,
            deductibleCurrencyCode: $validated['deductible_currency_code'] ?? null,
            startsAt: $validated['starts_at'] ?? null,
            endsAt: $validated['ends_at'] ?? null,
            isScheduledItem: (bool) ($validated['is_scheduled_item'] ?? false),
            contactName: $validated['contact_name'] ?? null,
            contactEmail: $validated['contact_email'] ?? null,
            contactPhone: $validated['contact_phone'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new InsuranceRecordResource($record)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('insuranceRecord');
        $record = $copy->insuranceRecords()->findOrFail($recordId);

        $validated = $request->validate([
            'provider' => ['required', 'string', 'max:255'],
            'insured_value' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', Rule::enum(InsuranceStatus::class)],
            'currency_code' => ['nullable', 'string', 'size:3'],
            'policy_number' => ['nullable', 'string', 'max:255'],
            'coverage_type' => ['nullable', 'string', 'max:255'],
            'deductible_amount' => ['nullable', 'integer', 'min:0'],
            'deductible_currency_code' => ['nullable', 'string', 'size:3'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_scheduled_item' => ['nullable', 'boolean'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $record = new UpdateInsuranceRecord(
            user: $request->user(),
            record: $record,
            provider: $validated['provider'],
            insuredValue: $validated['insured_value'],
            status: InsuranceStatus::from($validated['status'] ?? InsuranceStatus::Active->value),
            currencyCode: $validated['currency_code'] ?? null,
            policyNumber: $validated['policy_number'] ?? null,
            coverageType: $validated['coverage_type'] ?? null,
            deductibleAmount: $validated['deductible_amount'] ?? null,
            deductibleCurrencyCode: $validated['deductible_currency_code'] ?? null,
            startsAt: $validated['starts_at'] ?? null,
            endsAt: $validated['ends_at'] ?? null,
            isScheduledItem: (bool) ($validated['is_scheduled_item'] ?? false),
            contactName: $validated['contact_name'] ?? null,
            contactEmail: $validated['contact_email'] ?? null,
            contactPhone: $validated['contact_phone'] ?? null,
            note: $validated['note'] ?? null,
        )->execute();

        return new InsuranceRecordResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.catalog', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('insuranceRecord');
        $record = $copy->insuranceRecords()->findOrFail($recordId);

        new DestroyInsuranceRecord(
            user: $request->user(),
            record: $record,
        )->execute();

        return response()->noContent(204);
    }
}
