<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$valuation = fn (string $id, string $type, int $amount, string $confidence): array => [
    'type' => 'valuation',
    'id' => $id,
    'attributes' => [
        'copy_id' => '1',
        'type' => $type,
        'amount' => $amount,
        'currency_code' => 'USD',
        'valued_at' => 1752537600,
        'confidence' => $confidence,
        'valuer' => null,
        'method' => null,
        'source_url' => null,
        'reference_number' => null,
        'note' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/copies/1/valuations/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of valuations to return per page, between 1 and 100.',
        'default' => '10',
    ],
    [
        'name' => 'page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The page number to return.',
        'default' => '1',
    ],
];

$copyId = [
    'name' => 'copy',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the copy the valuation belongs to.',
];

$valuationId = [
    'name' => 'valuation',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the valuation.',
];

$bodyParams = [
    [
        'name' => 'type',
        'type' => 'string',
        'required' => true,
        'description' => 'Where the valuation came from. One of user_estimate, professional_appraisal, market_estimate, insurance_value, auction_estimate, automated_estimate or other.',
        'example' => 'professional_appraisal',
    ],
    [
        'name' => 'amount',
        'type' => 'integer',
        'required' => true,
        'description' => 'What the copy was reckoned to be worth, in the smallest currency unit (e.g. cents).',
        'example' => '25000',
    ],
    [
        'name' => 'valued_at',
        'type' => 'string',
        'required' => true,
        'description' => 'The date the copy was valued, in YYYY-MM-DD format. This is what orders the valuations, so the latest date is read as the current estimated value.',
        'example' => '2024-01-15',
    ],
    [
        'name' => 'currency_code',
        'type' => 'string',
        'required' => false,
        'description' => 'The three letter currency code the amount is expressed in. Defaults to the currency of the collection.',
        'example' => 'USD',
    ],
    [
        'name' => 'confidence',
        'type' => 'string',
        'required' => false,
        'description' => 'How much weight the valuation carries. One of low, medium, high or unknown. Defaults to unknown.',
        'example' => 'high',
    ],
    [
        'name' => 'valuer',
        'type' => 'string',
        'required' => false,
        'description' => 'Who or what produced the valuation, such as the appraiser or the marketplace.',
        'example' => 'Central Perk Appraisals',
    ],
    [
        'name' => 'method',
        'type' => 'string',
        'required' => false,
        'description' => 'How the figure was arrived at, such as comparable sales.',
        'example' => 'Comparable sales',
    ],
    [
        'name' => 'source_url',
        'type' => 'string',
        'required' => false,
        'description' => 'A link to where the valuation can be checked, such as the appraisal report or the listing.',
        'example' => 'https://example.com/appraisals/1994',
    ],
    [
        'name' => 'reference_number',
        'type' => 'string',
        'required' => false,
        'description' => 'An appraisal or report reference for the valuation.',
        'example' => 'CP-1994',
    ],
    [
        'name' => 'note',
        'type' => 'string',
        'required' => false,
        'description' => 'A free form note about the valuation.',
        'example' => 'Valued after the Central Perk auction.',
    ],
];

return [
    'name' => 'Valuations',
    'sections' => [
        [
            'id' => 'valuations-list',
            'title' => 'List valuations',
            'method' => 'GET',
            'path' => '/copies/{copy}/valuations',
            'examplePath' => '/copies/1/valuations',
            'description' => 'Retrieve the valuations recorded against a copy: what it has been reckoned to be worth over time. They are returned most recent first.',
            'body' => [
                'Valuations are append-only. Changing what a copy is worth records a new valuation rather than overwriting an old one, and the latest by `valued_at` is what the application shows as the current estimated value.',
                'A purchase price is not a valuation: what was actually paid belongs to a transaction. A valuation is only ever an estimate of worth.',
            ],
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of valuation objects.',
            'response' => ApiDocumentation::paginated([
                $valuation('2', 'professional_appraisal', 25000, 'high'),
                $valuation('1', 'user_estimate', 10000, 'unknown'),
            ], '/copies/1/valuations'),
        ],
        [
            'id' => 'valuations-show',
            'title' => 'Get a valuation',
            'label' => 'Get a valuation',
            'method' => 'GET',
            'path' => '/copies/{copy}/valuations/{valuation}',
            'examplePath' => '/copies/1/valuations/1',
            'description' => 'Retrieve a single valuation of a copy by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$copyId, $valuationId],
            'returns' => 'A valuation object, or 404 when the valuation does not belong to your account.',
            'response' => ['data' => $valuation('1', 'user_estimate', 10000, 'unknown')],
        ],
        [
            'id' => 'valuations-create',
            'title' => 'Create a valuation',
            'label' => 'Create a valuation',
            'method' => 'POST',
            'path' => '/copies/{copy}/valuations',
            'examplePath' => '/copies/1/valuations',
            'description' => 'Record a valuation against a copy. This is the normal way to change what a copy is worth, since valuations are kept as history rather than edited in place.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created valuation object.',
            'responseStatus' => 201,
            'response' => ['data' => $valuation('1', 'professional_appraisal', 25000, 'high')],
        ],
        [
            'id' => 'valuations-update',
            'title' => 'Update a valuation',
            'label' => 'Update a valuation',
            'method' => 'PUT',
            'path' => '/copies/{copy}/valuations/{valuation}',
            'examplePath' => '/copies/1/valuations/1',
            'description' => 'Update a valuation, for correcting a figure that was entered wrong. Every field is replaced, so send the ones you want to keep along with the ones you are changing.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $valuationId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated valuation object.',
            'response' => ['data' => $valuation('1', 'professional_appraisal', 25000, 'high')],
        ],
        [
            'id' => 'valuations-destroy',
            'title' => 'Delete a valuation',
            'label' => 'Delete a valuation',
            'method' => 'DELETE',
            'path' => '/copies/{copy}/valuations/{valuation}',
            'examplePath' => '/copies/1/valuations/1',
            'description' => 'Delete a valuation. Deleting the latest one hands the current estimated value back to the valuation before it.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$copyId, $valuationId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
