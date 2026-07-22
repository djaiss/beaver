<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$item = fn (string $id, string $name, ?string $description): array => [
    'type' => 'item',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'description' => $description,
        'collection_id' => '1',
        'type_id' => null,
        'category_id' => null,
        'set_id' => null,
        'series_id' => null,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collections/1/items/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of items to return per page, between 1 and 100.',
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

$collectionId = [
    'name' => 'collection',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the collection the item belongs to.',
];

$itemId = [
    'name' => 'item',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the item.',
];

$nameParam = [
    'name' => 'name',
    'type' => 'string',
    'required' => true,
    'description' => 'The name of the item. Maximum 255 characters.',
    'example' => 'Amazing Spider-Man #1',
];

$descriptionParam = [
    'name' => 'description',
    'type' => 'string',
    'required' => false,
    'description' => 'A free text description of the item. Maximum 2000 characters.',
    'example' => 'Near mint condition.',
];

$typeParam = [
    'name' => 'type_id',
    'type' => 'integer',
    'required' => false,
    'description' => 'The ID of the type of the item. Must be a type of your account.',
    'example' => '3',
];

$categoryParam = [
    'name' => 'category_id',
    'type' => 'integer',
    'required' => false,
    'description' => 'The ID of the category the item sits in. Must belong to the collection.',
    'example' => '1',
];

$setParam = [
    'name' => 'set_id',
    'type' => 'integer',
    'required' => false,
    'description' => 'The ID of the set the item is part of. Must be a set of the same collection as the item.',
    'example' => '1',
];

$seriesParam = [
    'name' => 'series_id',
    'type' => 'integer',
    'required' => false,
    'description' => 'The ID of the series the item belongs to. A series is account-wide, so it only has to belong to your account, not to the item\'s collection.',
    'example' => '1',
];

return [
    'name' => 'Items',
    'sections' => [
        [
            'id' => 'items-list',
            'title' => 'List items',
            'method' => 'GET',
            'path' => '/collections/{collection}/items',
            'examplePath' => '/collections/1/items',
            'description' => 'Retrieve the items catalogued within a collection.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of item objects.',
            'response' => ApiDocumentation::paginated([
                $item('1', 'Amazing Spider-Man #1', 'Near mint condition.'),
                $item('2', 'Amazing Spider-Man #2', null),
            ], '/collections/1/items'),
        ],
        [
            'id' => 'items-show',
            'title' => 'Get an item',
            'label' => 'Get an item',
            'method' => 'GET',
            'path' => '/collections/{collection}/items/{item}',
            'examplePath' => '/collections/1/items/1',
            'description' => 'Retrieve a single item of a collection by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId, $itemId],
            'returns' => 'An item object, or 404 when the item does not belong to your account.',
            'response' => ['data' => $item('1', 'Amazing Spider-Man #1', 'Near mint condition.')],
        ],
        [
            'id' => 'items-create',
            'title' => 'Create an item',
            'label' => 'Create an item',
            'method' => 'POST',
            'path' => '/collections/{collection}/items',
            'examplePath' => '/collections/1/items',
            'description' => 'Create an item within a collection.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId],
            'bodyParams' => [$nameParam, $descriptionParam, $typeParam, $categoryParam, $setParam, $seriesParam],
            'returns' => 'The created item object.',
            'responseStatus' => 201,
            'response' => ['data' => $item('1', 'Amazing Spider-Man #1', 'Near mint condition.')],
        ],
        [
            'id' => 'items-update',
            'title' => 'Update an item',
            'label' => 'Update an item',
            'method' => 'PUT',
            'path' => '/collections/{collection}/items/{item}',
            'examplePath' => '/collections/1/items/1',
            'description' => 'Update the name, description and type of an item.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId, $itemId],
            'bodyParams' => [$nameParam, $descriptionParam, $typeParam],
            'returns' => 'The updated item object.',
            'response' => ['data' => $item('1', 'Amazing Spider-Man #1', 'Near mint condition.')],
        ],
        [
            'id' => 'items-destroy',
            'title' => 'Delete an item',
            'label' => 'Delete an item',
            'method' => 'DELETE',
            'path' => '/collections/{collection}/items/{item}',
            'examplePath' => '/collections/1/items/1',
            'description' => 'Delete an item.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId, $itemId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
