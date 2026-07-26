<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

return [
    'name' => 'Dashboard',
    'sections' => [
        [
            'id' => 'dashboard-show',
            'title' => 'Get the dashboard of your account',
            'label' => 'Get the dashboard',
            'method' => 'GET',
            'path' => '/dashboard',
            'examplePath' => '/dashboard',
            'description' => 'Retrieve the aggregates behind the dashboard: what the account holds, what it is worth, what was catalogued last, where custody stands and where the value physically sits.',
            'body' => [
                'These are computed values rather than stored records, so the object has no created_at or updated_at. Every amount is an integer in cents. A collection may name a currency of its own, but an account wide roll up has to settle on one, so every amount here reads in the currency of the account, returned as currency.',
                'Amounts come from the copies of the items, so an item with no copy counts towards items and contributes nothing to any amount. A copy nobody has valued counts towards copies but not towards valued_copies, which is how you spot the gap. What a copy is worth is its most recent valuation.',
                'value_added_this_month sums what was acquired since the first of the month, by the date of the transaction that brought each copy in. A copy with no such transaction is left out of it. collections lists the four most recently updated collections, recent_additions the five newest items, and value_by_location the five locations holding the most value. A location label is null for the copies filed under no location.',
            ],
            'permissions' => 'Any member of the account.',
            'returns' => 'An account_dashboard object.',
            'response' => [
                'data' => [
                    'type' => 'account_dashboard',
                    'id' => '1',
                    'attributes' => [
                        'currency' => 'USD',
                        'totals' => [
                            'collections' => 4,
                            'items' => 567,
                            'copies' => 677,
                            'valued_copies' => 654,
                            'value' => 2031000,
                            'average' => 3582,
                            'items_added_this_month' => 18,
                            'value_added_this_month' => 124000,
                        ],
                        'collections' => [
                            [
                                'collection_id' => '1',
                                'collection_name' => 'Marvel Comics 1990s',
                                'visibility' => 'shared',
                                'items' => 142,
                                'copies' => 168,
                                'value' => 842000,
                                'updated_at' => '2026-08-03T09:12:44+00:00',
                            ],
                        ],
                        'recent_additions' => [
                            [
                                'id' => '918',
                                'name' => 'Amazing Spider-Man #365',
                                'collection_id' => '1',
                                'collection_name' => 'Marvel Comics 1990s',
                                'condition' => 'Near Mint',
                                'location' => 'Box A1',
                                'copies' => 1,
                                'created_at' => '2026-08-03T09:12:44+00:00',
                            ],
                        ],
                        'loans' => [
                            'outgoing' => 5,
                            'incoming' => 2,
                            'overdue' => 2,
                            'dueSoon' => 3,
                            'planned' => 2,
                            'returned' => 4,
                            'deposits' => ['USD' => 105000],
                        ],
                        'value_by_location' => [
                            ['label' => 'Display Case', 'value' => 684000],
                            ['label' => null, 'value' => 42000],
                        ],
                    ],
                    'links' => [
                        'self' => $base.'/dashboard',
                    ],
                ],
            ],
        ],
    ],
];
