<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection\Copy;

use App\Actions\CreateMaintenanceRecord;
use App\Actions\DestroyMaintenanceRecord;
use App\Actions\UpdateMaintenanceRecord;
use App\Enums\MaintenanceType;
use App\Http\Controllers\Controller;
use App\Http\Resources\MaintenanceRecordResource;
use App\Models\Copy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class MaintenanceRecordController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);

        $perPage = max(1, min((int) $request->query('per_page', 10), config('app.maximum_items_per_page')));

        $records = $copy->maintenanceRecords()->paginate($perPage);

        return MaintenanceRecordResource::collection($records);
    }

    public function show(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('maintenanceRecord');
        $record = $copy->maintenanceRecords()->findOrFail($recordId);

        return new MaintenanceRecordResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function create(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(MaintenanceType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'performed_at' => ['nullable', 'date'],
            'cost_amount' => ['nullable', 'integer', 'min:0'],
            'cost_currency_code' => ['nullable', 'string', 'size:3'],
            'item_condition_before_id' => ['nullable', 'integer'],
            'item_condition_after_id' => ['nullable', 'integer'],
            'next_due_at' => ['nullable', 'date'],
            'include_in_provenance' => ['nullable', 'boolean'],
        ]);

        $record = new CreateMaintenanceRecord(
            user: $request->user(),
            copy: $copy,
            type: MaintenanceType::from($validated['type']),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            performedBy: $validated['performed_by'] ?? null,
            performedAt: $validated['performed_at'] ?? null,
            costAmount: $validated['cost_amount'] ?? null,
            costCurrencyCode: $validated['cost_currency_code'] ?? null,
            itemConditionBeforeId: $validated['item_condition_before_id'] ?? null,
            itemConditionAfterId: $validated['item_condition_after_id'] ?? null,
            nextDueAt: $validated['next_due_at'] ?? null,
            includeInProvenance: (bool) ($validated['include_in_provenance'] ?? false),
        )->execute();

        return new MaintenanceRecordResource($record)
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request): JsonResponse
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('maintenanceRecord');
        $record = $copy->maintenanceRecords()->findOrFail($recordId);

        $validated = $request->validate([
            'type' => ['required', Rule::enum(MaintenanceType::class)],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'performed_by' => ['nullable', 'string', 'max:255'],
            'performed_at' => ['nullable', 'date'],
            'cost_amount' => ['nullable', 'integer', 'min:0'],
            'cost_currency_code' => ['nullable', 'string', 'size:3'],
            'item_condition_before_id' => ['nullable', 'integer'],
            'item_condition_after_id' => ['nullable', 'integer'],
            'next_due_at' => ['nullable', 'date'],
            'include_in_provenance' => ['nullable', 'boolean'],
        ]);

        $record = new UpdateMaintenanceRecord(
            user: $request->user(),
            record: $record,
            type: MaintenanceType::from($validated['type']),
            title: $validated['title'],
            description: $validated['description'] ?? null,
            performedBy: $validated['performed_by'] ?? null,
            performedAt: $validated['performed_at'] ?? null,
            costAmount: $validated['cost_amount'] ?? null,
            costCurrencyCode: $validated['cost_currency_code'] ?? null,
            itemConditionBeforeId: $validated['item_condition_before_id'] ?? null,
            itemConditionAfterId: $validated['item_condition_after_id'] ?? null,
            nextDueAt: $validated['next_due_at'] ?? null,
            includeInProvenance: (bool) ($validated['include_in_provenance'] ?? false),
        )->execute();

        return new MaintenanceRecordResource($record)
            ->response()
            ->setStatusCode(200);
    }

    public function destroy(Request $request): Response
    {
        $copyId = $request->route()->parameter('copy');
        $account = $request->user()->account;
        $copy = Copy::whereRelation('item.collection', 'account_id', $account->id)->findOrFail($copyId);
        $recordId = $request->route()->parameter('maintenanceRecord');
        $record = $copy->maintenanceRecords()->findOrFail($recordId);

        new DestroyMaintenanceRecord(
            user: $request->user(),
            record: $record,
        )->execute();

        return response()->noContent(204);
    }
}
