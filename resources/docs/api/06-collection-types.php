<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$type = fn (string $id, string $name, string $color): array => [
    'type' => 'collection_type',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'color' => $color,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collection-types/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of collection types to return per page, between 1 and 100.',
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

$typeId = [
    'name' => 'collectionType',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the collection type.',
];

return [
    'name' => 'Collection types',
    'sections' => [
        [
            'id' => 'collection-types-list',
            'title' => 'List collection types',
            'label' => 'List types',
            'method' => 'GET',
            'path' => '/collection-types',
            'description' => 'Retrieve the collection types of your account, most recently updated first. A collection type is a user-defined category (Comics, Vinyl, Wine) that decides which custom fields apply to an item.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of collection_type objects.',
            'response' => ApiDocumentation::paginated([
                $type('2', 'Wine', '#8b5cf6'),
                $type('1', 'Comics', '#fb923c'),
            ], '/collection-types'),
        ],
        [
            'id' => 'collection-types-show',
            'title' => 'Get a collection type',
            'label' => 'Get a type',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}',
            'examplePath' => '/collection-types/1',
            'description' => 'Retrieve a single collection type of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$typeId],
            'returns' => 'A collection_type object, or 404 when the type does not belong to your account.',
            'response' => ['data' => $type('1', 'Comics', '#fb923c')],
        ],
        [
            'id' => 'collection-types-create',
            'title' => 'Create a collection type',
            'label' => 'Create a type',
            'method' => 'POST',
            'path' => '/collection-types',
            'description' => 'Create a collection type. Add custom fields to it with the custom fields endpoints, and link it to collections with the sync endpoint below.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the type. Maximum 255 characters.',
                    'example' => 'Comics',
                ],
                [
                    'name' => 'color',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The color of the type as a six-digit hex code, such as #fb923c.',
                    'default' => '#6B7280',
                    'example' => '#fb923c',
                ],
            ],
            'returns' => 'The created collection_type object.',
            'responseStatus' => 201,
            'response' => ['data' => $type('1', 'Comics', '#fb923c')],
        ],
        [
            'id' => 'collection-types-update',
            'title' => 'Update a collection type',
            'label' => 'Update a type',
            'method' => 'PUT',
            'path' => '/collection-types/{collectionType}',
            'examplePath' => '/collection-types/1',
            'description' => 'Update the name and color of a collection type.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the type. Maximum 255 characters.',
                    'example' => 'Comics',
                ],
                [
                    'name' => 'color',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The color of the type as a six-digit hex code, such as #fb923c.',
                    'example' => '#fb923c',
                ],
            ],
            'returns' => 'The updated collection_type object.',
            'response' => ['data' => $type('1', 'Comics', '#fb923c')],
        ],
        [
            'id' => 'collection-types-sync-collections',
            'title' => 'Set the collections of a type',
            'label' => 'Set its collections',
            'method' => 'PUT',
            'path' => '/collection-types/{collectionType}/collections',
            'examplePath' => '/collection-types/1/collections',
            'description' => 'Set which collections the type applies to. The list you send replaces the previous one entirely. IDs of collections that do not belong to your account are ignored.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'bodyParams' => [
                [
                    'name' => 'collection_ids',
                    'type' => 'array of integers',
                    'required' => false,
                    'description' => 'The IDs of the collections the type applies to. Send an empty array to unlink the type from every collection.',
                    'example' => [1, 2],
                ],
            ],
            'returns' => 'The collection_type object.',
            'response' => ['data' => $type('1', 'Comics', '#fb923c')],
        ],
        [
            'id' => 'collection-types-destroy',
            'title' => 'Delete a collection type',
            'label' => 'Delete a type',
            'method' => 'DELETE',
            'path' => '/collection-types/{collectionType}',
            'examplePath' => '/collection-types/1',
            'description' => 'Delete a collection type, together with its custom fields and its links to collections.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
