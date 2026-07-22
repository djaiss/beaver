<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$entry = fn (string $id, string $source, string $sourceId, string $title, ?string $summary, ?int $date, string $precision, ?int $amount, ?string $currency, bool $meaningful): array => [
    'type' => 'timeline_entry',
    'id' => $id,
    'attributes' => [
        'source_type' => $source,
        'source_id' => $sourceId,
        'title' => $title,
        'summary' => $summary,
        'date' => $date,
        'date_precision' => $precision,
        'amount' => $amount,
        'currency_code' => $currency,
        'meaningful' => $meaningful,
    ],
];

$copyId = [
    'name' => 'copy',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the copy whose history you are reading.',
];

$queryParams = [
    [
        'name' => 'view',
        'type' => 'string',
        'required' => false,
        'description' => 'Which entries to include. meaningful returns only the historically meaningful ones; complete adds the routine records too, such as ordinary location moves, informal loans and routine maintenance.',
        'default' => 'meaningful',
    ],
    [
        'name' => 'type',
        'type' => 'array',
        'required' => false,
        'description' => 'Filter to one or more sources, sent as repeated type parameters (type[]=valuation&type[]=loan). One of transaction, provenance, valuation, insurance, maintenance, loan or location. Omit to include every source.',
        'example' => 'valuation',
    ],
];

return [
    'name' => 'Copy history',
    'sections' => [
        [
            'id' => 'copy-history-list',
            'title' => 'Get a copy\'s history',
            'method' => 'GET',
            'path' => '/copies/{copy}/history',
            'examplePath' => '/copies/1/history',
            'description' => 'Retrieve the unified history of a copy: its qualifying transactions, provenance events, valuations, insurance records, meaningful maintenance, loans and returns, and location moves, merged into one chronological read, newest first.',
            'body' => [
                'The history is a read model, not a stored table. Every entry is assembled from a source record at read time, and each source stays the source of truth for its own data. An entry names the source it came from through source_type and source_id, so you can fetch the full record from its own endpoint.',
                'Only the historically meaningful entries are returned by default. Pass view=complete to add the routine records. Undated entries, and provenance events recorded as unknown, sort to the end. Amounts are returned in cents in the currency they were recorded in, and are never converted.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $queryParams,
            'returns' => 'A list of timeline entry objects, newest first.',
            'response' => [
                'data' => [
                    $entry('location-9', 'location', '9', 'Moved to Secure storage', 'Climate-controlled unit', 1782777600, 'exact', null, null, false),
                    $entry('loan-4-return', 'loan', '4', 'Returned from Montreal Museum of Fine Arts', 'Condition in: Excellent', 1743465600, 'exact', null, null, true),
                    $entry('valuation-5', 'valuation', '5', 'Professional appraisal', 'Jane Smith', 1325376000, 'exact', 450000, 'CAD', true),
                    $entry('provenance-2', 'provenance', '2', 'Acquired from Sotheby\'s', 'Purchased at the London spring sale.', 542764800, 'month', null, null, true),
                ],
            ],
        ],
    ],
];
