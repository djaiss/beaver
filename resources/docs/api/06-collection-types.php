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

$export = [
    'type' => 'collection_type_export',
    'id' => '1',
    'attributes' => [
        'schema' => [
            'schemaVersion' => 1,
            'type' => [
                'name' => 'Comics',
                'color' => '#fb923c',
                'groups' => [
                    [
                        'name' => 'Publishing info',
                        'fields' => [
                            ['name' => 'Issue #', 'type' => 'number'],
                            ['name' => 'Publisher', 'type' => 'select', 'options' => ['Marvel', 'DC', 'Image']],
                        ],
                    ],
                ],
                'standaloneFields' => [
                    ['name' => 'Signed', 'type' => 'boolean'],
                ],
            ],
        ],
    ],
    'links' => [
        'self' => $base.'/collection-types/1/export',
        'collection_type' => $base.'/collection-types/1',
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
            'id' => 'collection-types-export',
            'title' => 'Export a collection type',
            'label' => 'Export a type',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}/export',
            'examplePath' => '/collection-types/1/export',
            'description' => 'Retrieve the full schema of a collection type as a portable JSON document: its name, its color, its field groups, and every custom field with its kind, its ordering and its select options. This is the same document the app hands out from the type\'s export screen.',
            'body' => [
                'The document describes structure only. Your items, their copies and their photos are never part of it.',
                'Fields are ordered the way they render on an item, and a field only carries an options key when it has options. Groups are listed under groups, and fields that sit outside of any group under standaloneFields.',
                'The schemaVersion key tells you which shape of the document you are reading. It is bumped whenever the shape changes, so store it alongside the export.',
            ],
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'returns' => 'A collection_type_export object whose schema attribute is the portable JSON document, or 404 when the type does not belong to your account.',
            'response' => ['data' => $export],
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
        [
            'id' => 'collection-types-import',
            'title' => 'Import a collection type',
            'label' => 'Import a type',
            'method' => 'POST',
            'path' => '/collection-types/import',
            'description' => 'Rebuild a collection type from a JSON document, in the exact shape the export endpoint hands out. This creates a new type with its groups and fields. It never updates an existing one, so importing the same document twice gives you two types.',
            'body' => [
                'The document is sent as a string in the json parameter, not as a nested object. A document that is not valid JSON, that is missing the type key, or that names a field type the app does not know is rejected with a 422 response.',
            ],
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'json',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The exported document, as a JSON encoded string. Maximum 100000 characters.',
                    'example' => '{"schemaVersion":1,"type":{"name":"Comics","color":"#fb923c","groups":[],"standaloneFields":[]}}',
                ],
            ],
            'returns' => 'The created collection_type object.',
            'responseStatus' => 201,
            'response' => ['data' => $type('1', 'Comics', '#fb923c')],
        ],
    ],
];
