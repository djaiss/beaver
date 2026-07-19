<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$collectionId = [
    'name' => 'collection',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the collection.',
];

return [
    'name' => 'Statistics',
    'sections' => [
        [
            'id' => 'statistics-show',
            'title' => 'Get the statistics of a collection',
            'label' => 'Get the statistics',
            'method' => 'GET',
            'path' => '/collections/{collection}/statistics',
            'examplePath' => '/collections/1/statistics',
            'description' => 'Retrieve the aggregates behind the statistics screen of a collection: what it holds, what it is worth, how it grew and where it is stored.',
            'body' => [
                'These are computed values rather than stored records, so the object has no created_at or updated_at. Every amount is an integer in cents, in the currency of the collection. Values come from the copies of the items, so an item with no copy counts towards items and contributes nothing to any amount.',
                'value_over_time and acquisitions_per_month cover a rolling twelve month window, one entry per month, labelled with the short month name in the locale of your user. value_over_time is a running total, so its first point already carries everything acquired before the window opened.',
                'sets_completion is null unless at least one set of the collection declares a target_count. A label is null where the copies have no condition or no location. In by_category, the entry with other set to true sums the categories too small to be named individually.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId],
            'returns' => 'A collection_statistics object, or 404 when the collection does not belong to your account.',
            'response' => [
                'data' => [
                    'type' => 'collection_statistics',
                    'id' => '1',
                    'attributes' => [
                        'totals' => [
                            'items' => 42,
                            'copies' => 57,
                            'value' => 128500,
                            'average' => 3059,
                            'items_added_this_month' => 5,
                            'value_added_this_month' => 32500,
                            'undated_copies' => 4,
                        ],
                        'sets_completion' => [
                            'percentage' => 67,
                            'owned' => 8,
                            'target' => 12,
                            'remaining' => 4,
                            'sets' => 1,
                        ],
                        'value_over_time' => [
                            ['label' => 'May', 'value' => 96000],
                            ['label' => 'Jun', 'value' => 128500],
                        ],
                        'acquisitions_per_month' => [
                            ['label' => 'May', 'count' => 3],
                            ['label' => 'Jun', 'count' => 5],
                        ],
                        'by_category' => [
                            ['label' => 'Silver Age', 'other' => false, 'count' => 18, 'percentage' => 43],
                            ['label' => null, 'other' => true, 'count' => 24, 'percentage' => 57],
                        ],
                        'by_condition' => [
                            ['label' => 'Near Mint', 'count' => 21, 'percentage' => 37],
                        ],
                        'value_by_location' => [
                            ['label' => 'Shelf A', 'value' => 91000],
                        ],
                        'top_items' => [
                            [
                                'id' => '7',
                                'name' => 'Amazing Spider-Man #1',
                                'value' => 42000,
                                'condition' => 'Near Mint',
                                'location' => 'Shelf A',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => $base.'/collections/1/statistics',
                    ],
                ],
            ],
        ],
    ],
];
