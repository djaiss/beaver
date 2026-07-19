<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$copy = fn (string $id, ?string $conditionId, ?int $estimatedValue): array => [
    'type' => 'copy',
    'id' => $id,
    'attributes' => [
        'item_id' => '1',
        'identifier' => null,
        'condition_id' => $conditionId,
        'current_location_id' => null,
        'status' => 'owned',
        'quantity' => 1,
        'disposed_at' => null,
        'note' => null,
        'estimated_value' => $estimatedValue,
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
        'name' => 'identifier',
        'type' => 'string',
        'required' => false,
        'description' => 'A reference of your own for this copy, such as a serial or a shelf number.',
        'example' => 'CP-0042',
    ],
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
        'name' => 'status',
        'type' => 'string',
        'required' => false,
        'description' => 'Where the copy sits in its lifecycle. One of owned, ordered, loaned, sold, gifted, lost, stolen, disposed or other. Defaults to owned.',
        'example' => 'owned',
    ],
    [
        'name' => 'quantity',
        'type' => 'integer',
        'required' => false,
        'description' => 'How many identical units this copy stands for. At least 1, and defaults to 1.',
        'example' => '1',
    ],
    [
        'name' => 'disposed_at',
        'type' => 'string',
        'required' => false,
        'description' => 'The date the copy left the collection, in YYYY-MM-DD format.',
        'example' => '2024-01-15',
    ],
    [
        'name' => 'note',
        'type' => 'string',
        'required' => false,
        'description' => 'A free form note about the copy.',
        'example' => 'Signed by the artist.',
    ],
    [
        'name' => 'estimated_value',
        'type' => 'integer',
        'required' => false,
        'description' => 'What the copy is currently reckoned to be worth, in the smallest currency unit (e.g. cents). Recorded as a new valuation rather than overwriting the previous one.',
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
            'description' => 'Retrieve the physical copies owned of an item. Each copy carries its own identifier, condition, location and status.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of copy objects.',
            'response' => ApiDocumentation::paginated([
                $copy('1', '1', 5000),
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
            'response' => ['data' => $copy('1', '1', 5000)],
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
            'response' => ['data' => $copy('1', '1', 5000)],
        ],
        [
            'id' => 'copies-update',
            'title' => 'Update a copy',
            'label' => 'Update a copy',
            'method' => 'PUT',
            'path' => '/items/{item}/copies/{copy}',
            'examplePath' => '/items/1/copies/1',
            'description' => 'Update the identifier, condition, location, status and estimated value of a copy.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId, $copyId],
            'bodyParams' => $bodyParams,
            'returns' => 'The updated copy object.',
            'response' => ['data' => $copy('1', '1', 5000)],
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
