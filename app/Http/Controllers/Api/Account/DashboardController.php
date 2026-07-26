<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Account;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Services\AccountDashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * The aggregates behind the dashboard, read across the whole account. These
     * are computed values rather than a model, so they are returned as one object
     * instead of an Eloquent resource.
     */
    public function index(Request $request): JsonResponse
    {
        $account = $request->user()->account;
        $dashboard = new AccountDashboard(account: $account);

        $totals = $dashboard->totals();

        return response()->json([
            'data' => [
                'type' => 'account_dashboard',
                'id' => (string) $account->id,
                'attributes' => [
                    'currency' => $account->currency_code,
                    'totals' => [
                        'collections' => $totals['collections'],
                        'items' => $totals['items'],
                        'copies' => $totals['copies'],
                        'valued_copies' => $totals['valuedCopies'],
                        'value' => $totals['value'],
                        'average' => $totals['average'],
                        'items_added_this_month' => $totals['itemsAddedThisMonth'],
                        'value_added_this_month' => $totals['valueAddedThisMonth'],
                    ],
                    'collections' => $this->collections($dashboard),
                    'recent_additions' => $this->recentAdditions($dashboard),
                    'loans' => $dashboard->loanSnapshot(),
                    'value_by_location' => $dashboard->valueByLocation(),
                ],
                'links' => [
                    'self' => route('api.dashboard'),
                ],
            ],
        ]);
    }

    /**
     * The service hands back the Catalog model itself, which the screen needs to
     * link to and to badge. The API only publishes what identifies the collection,
     * so the rest of the row never leaks into the response.
     *
     * @return list<array<string, mixed>>
     */
    private function collections(AccountDashboard $dashboard): array
    {
        return array_map(fn (array $row): array => [
            'collection_id' => (string) $row['catalog']->id,
            'collection_name' => $row['catalog']->name,
            'visibility' => $row['catalog']->visibility->value,
            'items' => $row['items'],
            'copies' => $row['copies'],
            'value' => $row['value'],
            'updated_at' => $row['catalog']->updated_at?->toIso8601String(),
        ], $dashboard->collections());
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function recentAdditions(AccountDashboard $dashboard): array
    {
        return array_map(function (array $row): array {
            /** @var Item $item */
            $item = $row['item'];

            return [
                'id' => (string) $item->id,
                'name' => $item->name,
                'collection_id' => (string) $item->catalog_id,
                'collection_name' => $item->catalog->name,
                'condition' => $row['condition'],
                'location' => $row['location'],
                'copies' => $row['copies'],
                'created_at' => $item->created_at->toIso8601String(),
            ];
        }, $dashboard->recentAdditions());
    }
}
