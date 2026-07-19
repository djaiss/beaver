<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$copy = fn (string $id, ?string $conditionId, ?int $pricePaid): array => [
    'type' => 'copy',
    'id' => $id,
    'attributes' => [
        'item_id' => '1',
        'condition_id' => $conditionId,
        'location_id' => null,
        'acquired_at' => 1752537600,
        'price_paid' => $pricePaid,
        'estimated_value' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/items/1/copies/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of copies to return per page, between 1 and 100.',
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

$itemId = [
    'name' => 'item',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the item the copy belongs to.',
];

$copyId = [
    'name' => 'copy',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the copy.',
];

$bodyParams = [
    [
        'name' => 'condition_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the condition of the copy. Must be a condition of your account.',
        'example' => '1',
    ],
    [
        'name' => 'location_id',
        'type' => 'integer',
        'required' => false,
        'description' => 'The ID of the location where the copy is stored. Must be a location of your account.',
        'example' => '1',
    ],
    [
        'name' => 'acquired_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the copy was acquired, in YYYY-MM-DD format.',
        'example' => '2024-01-15',
    ],
    [
        'name' => 'price_paid',
        'type' => 'integer',
        'required' => false,
        'description' => 'The price paid for the copy, in the smallest currency unit (e.g. cents).',
        'example' => '1200',
    ],
    [
        'name' => 'estimated_value',
        'type' => 'integer',
        'required' => false,
        'description' => 'The estimated value of the copy, in the smallest currency unit (e.g. cents).',
        'example' => '5000',
    ],
];

return [
    'name' => 'Copies',
    'sections' => [
        [
            'id' => 'copies-list',
            'title' => 'List copies',
            'method' => 'GET',
            'path' => '/items/{item}/copies',
            'examplePath' => '/items/1/copies',
            'description' => 'Retrieve the physical copies owned of an item. Each copy carries its own condition, location and acquisition details.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of copy objects.',
            'response' => ApiDocumentation::paginated([
                $copy('1', '1', 1200),
                $copy('2', null, null),
            ], '/items/1/copies'),
        ],
        [
            'id' => 'copies-show',
            'title' => 'Get a copy',
            'label' => 'Get a copy',
            'method' => 'GET',
            'path' => '/items/{item}/copies/{copy}',
            'examplePath' => '/items/1/copies/1',
            'description' => 'Retrieve a single copy of an item by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId, $copyId],
            'returns' => 'A copy object, or 404 when the copy does not belong to your account.',
            'response' => ['data' => $copy('1', '1', 1200)],
        ],
        [
            'id' => 'copies-create',
            'title' => 'Create a copy',
            'label' => 'Create a copy',
            'method' => 'POST',
            'path' => '/items/{item}/copies',
            'examplePath' => '/items/1/copies',
            'description' => 'Create a copy of an item.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId],
            'bodyParams' => $bodyParams,
            'returns' => 'The created copy object.',
            'responseStatus' => 201,
            'response' => ['data' => $copy('1', '1', 1200)],
        ],
        [
            'id' => 'copies-update',
            'title' => 'Update a copy',
            'label' => 'Update a copy',
            'method' => 'PUT',
            'path' => '/items/{item}/copies/{copy}',
            'examplePath' => '/items/1/copies/1',
            'description' => 'Update the condition, location and acquisition details of a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId, $copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated copy object.',
            'response' => ['data' => $copy('1', '1', 1200)],
        ],
        [
            'id' => 'copies-destroy',
            'title' => 'Delete a copy',
            'label' => 'Delete a copy',
            'method' => 'DELETE',
            'path' => '/items/{item}/copies/{copy}',
            'examplePath' => '/items/1/copies/1',
            'description' => 'Delete a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId, $copyId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
