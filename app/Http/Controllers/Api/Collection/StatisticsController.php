<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Services\CollectionStatistics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * The aggregates behind the statistics screen of a collection. These are
     * computed values rather than a model, so they are returned as one object
     * instead of an Eloquent resource.
     */
    public function index(Request $request): JsonResponse
    {
        $collectionId = $request->route()->parameter('collection');

        $collection = $request->user()->account->collections()->findOrFail($collectionId);

        $statistics = new CollectionStatistics(collection: $collection);

        $totals = $statistics->totals();

        return response()->json([
            'data' => [
                'type' => 'collection_statistics',
                'id' => (string) $collection->id,
                'attributes' => [
                    'totals' => [
                        'items' => $totals['items'],
                        'copies' => $totals['copies'],
                        'value' => $totals['value'],
                        'average' => $totals['average'],
                        'items_added_this_month' => $totals['itemsAddedThisMonth'],
                        'value_added_this_month' => $totals['valueAddedThisMonth'],
                        'undated_copies' => $totals['undatedCopies'],
                    ],
                    'sets_completion' => $statistics->setsCompletion(),
                    'value_over_time' => $statistics->valueOverTime(),
                    'acquisitions_per_month' => $statistics->acquisitionsPerMonth(),
                    'by_category' => $statistics->byCategory(),
                    'by_condition' => $statistics->byCondition(),
                    'value_by_location' => $statistics->valueByLocation(),
                    'top_items' => $this->topItems($statistics),
                ],
                'links' => [
                    'self' => route('api.collections.statistics', $collection->id),
                ],
            ],
        ]);
    }

    /**
     * The service hands back the Item model itself, which the screen needs to
     * link to. The API only publishes what identifies the item, so the rest of
     * the row never leaks into the response.
     *
     * @return list<array<string, mixed>>
     */
    private function topItems(CollectionStatistics $statistics): array
    {
        return array_map(fn (array $row): array => [
            'id' => (string) $row['item']->id,
            'name' => $row['item']->name,
            'value' => $row['value'],
            'condition' => $row['condition'],
            'location' => $row['location'],
        ], $statistics->topItems());
    }
}
