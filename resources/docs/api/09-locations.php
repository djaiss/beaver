<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$location = fn (string $id, string $name, ?string $parentId, string $emoji): array => [
    'type' => 'location',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'emoji' => $emoji,
        'parent_id' => $parentId,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/locations/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of locations to return per page, between 1 and 100.',
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

$locationId = [
    'name' => 'location',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the location.',
];

return [
    'name' => 'Locations',
    'sections' => [
        [
            'id' => 'locations-list',
            'title' => 'List locations',
            'method' => 'GET',
            'path' => '/locations',
            'description' => 'Retrieve the storage locations of your account: the shelves, boxes and rooms where items are physically stored. Locations nest through parent_id; rebuild the tree client side from the flat list.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of location objects.',
            'response' => ApiDocumentation::paginated([
                $location('1', 'Shelf A', null, '📚'),
                $location('2', 'Box 1', '1', '📦'),
            ], '/locations'),
        ],
        [
            'id' => 'locations-show',
            'title' => 'Get a location',
            'label' => 'Get a location',
            'method' => 'GET',
            'path' => '/locations/{location}',
            'examplePath' => '/locations/2',
            'description' => 'Retrieve a single location of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$locationId],
            'returns' => 'A location object, or 404 when the location does not belong to your account.',
            'response' => ['data' => $location('2', 'Box 1', '1', '📦')],
        ],
        [
            'id' => 'locations-create',
            'title' => 'Create a location',
            'label' => 'Create a location',
            'method' => 'POST',
            'path' => '/locations',
            'description' => 'Create a location, optionally nested under another one.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the location. Maximum 255 characters.',
                    'example' => 'Box 1',
                ],
                [
                    'name' => 'parent_id',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The ID of the parent location, for nesting. Must belong to your account.',
                    'example' => 1,
                ],
                [
                    'name' => 'emoji',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The emoji shown next to the location name. One of 📦, 🏠, 🚪, 🛋️, 🗄️, 📚, 🧰, 🏢, 🚗, 🗃️, 🖼️ or 🎁.',
                    'example' => '📦',
                ],
            ],
            'returns' => 'The created location object.',
            'responseStatus' => 201,
            'response' => ['data' => $location('2', 'Box 1', '1', '📦')],
        ],
        [
            'id' => 'locations-update',
            'title' => 'Update a location',
            'label' => 'Update a location',
            'method' => 'PUT',
            'path' => '/locations/{location}',
            'examplePath' => '/locations/2',
            'description' => 'Update the name, parent and emoji of a location. Moves that would create a cycle are rejected with a 422 response: a location cannot be its own parent, or be nested under one of its own descendants.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$locationId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the location. Maximum 255 characters.',
                    'example' => 'Box 1',
                ],
                [
                    'name' => 'parent_id',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The ID of the parent location. Omit it or send null to move the location to the top level.',
                    'example' => 1,
                ],
                [
                    'name' => 'emoji',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The emoji shown next to the location name. One of 📦, 🏠, 🚪, 🛋️, 🗄️, 📚, 🧰, 🏢, 🚗, 🗃️, 🖼️ or 🎁.',
                    'example' => '📦',
                ],
            ],
            'returns' => 'The updated location object.',
            'response' => ['data' => $location('2', 'Box 1', '1', '📦')],
        ],
        [
            'id' => 'locations-destroy',
            'title' => 'Delete a location',
            'label' => 'Delete a location',
            'method' => 'DELETE',
            'path' => '/locations/{location}',
            'examplePath' => '/locations/1',
            'description' => 'Delete a location. Its nested child locations are deleted with it.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$locationId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
