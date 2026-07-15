<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$collection = fn (string $id, string $name, ?string $description, string $emoji, string $visibility): array => [
    'type' => 'collection',
    'id' => $id,
    'attributes' => [
        'uuid' => '9e2f6c1a-4b3d-4b6e-9a2f-0c1d2e3f4a5b',
        'name' => $name,
        'description' => $description,
        'emoji' => $emoji,
        'visibility' => $visibility,
        'currency' => 'USD',
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collections/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of collections to return per page, between 1 and 100.',
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
    'description' => 'The ID of the collection.',
];

return [
    'name' => 'Collections',
    'sections' => [
        [
            'id' => 'collections-list',
            'title' => 'List collections',
            'method' => 'GET',
            'path' => '/collections',
            'description' => 'Retrieve the collections of your account, most recently updated first.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of collection objects.',
            'response' => ApiDocumentation::paginated([
                $collection('2', 'Vinyl', null, '💿', 'shared'),
                $collection('1', 'My Comics', 'Silver age Marvel issues.', '📚', 'private'),
            ], '/collections'),
        ],
        [
            'id' => 'collections-show',
            'title' => 'Get a collection',
            'label' => 'Get a collection',
            'method' => 'GET',
            'path' => '/collections/{collection}',
            'examplePath' => '/collections/1',
            'description' => 'Retrieve a single collection of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId],
            'returns' => 'A collection object, or 404 when the collection does not belong to your account.',
            'response' => ['data' => $collection('1', 'My Comics', 'Silver age Marvel issues.', '📚', 'private')],
        ],
        [
            'id' => 'collections-create',
            'title' => 'Create a collection',
            'label' => 'Create a collection',
            'method' => 'POST',
            'path' => '/collections',
            'description' => 'Create a collection: a named set of items being catalogued, such as My Comics or Vinyl.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the collection. Maximum 255 characters.',
                    'example' => 'My Comics',
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'A description of the collection. Maximum 2000 characters.',
                    'example' => 'Silver age Marvel issues.',
                ],
                [
                    'name' => 'emoji',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The emoji shown next to the collection name. One of 📦, 📚, 💿, 🃏, 🍷, 🎮, 🧸, 🪙, 🖼️, ⌚, 👟 or 📷.',
                    'example' => '📚',
                ],
                [
                    'name' => 'visibility',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Who can see the collection. One of private (only you), shared (everyone in the account) or public (anyone with the link, read only).',
                    'example' => 'private',
                ],
                [
                    'name' => 'currency',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The three-letter currency code used for item values, such as USD or EUR.',
                    'example' => 'USD',
                ],
                [
                    'name' => 'collection_type_ids',
                    'type' => 'array of integers',
                    'required' => false,
                    'description' => 'The IDs of the collection types to link to the collection. IDs that do not belong to your account are ignored.',
                    'example' => [1],
                ],
            ],
            'returns' => 'The created collection object.',
            'responseStatus' => 201,
            'response' => ['data' => $collection('1', 'My Comics', 'Silver age Marvel issues.', '📚', 'private')],
        ],
        [
            'id' => 'collections-update',
            'title' => 'Update a collection',
            'label' => 'Update a collection',
            'method' => 'PUT',
            'path' => '/collections/{collection}',
            'examplePath' => '/collections/1',
            'description' => 'Update a collection. All the fields below are written on every call, so send the full object, not only the fields that changed.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the collection. Maximum 255 characters.',
                    'example' => 'My Comics',
                ],
                [
                    'name' => 'description',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'A description of the collection. Maximum 2000 characters.',
                    'example' => 'Silver age Marvel issues.',
                ],
                [
                    'name' => 'emoji',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The emoji shown next to the collection name. One of 📦, 📚, 💿, 🃏, 🍷, 🎮, 🧸, 🪙, 🖼️, ⌚, 👟 or 📷.',
                    'example' => '📚',
                ],
                [
                    'name' => 'visibility',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Who can see the collection. One of private, shared or public.',
                    'example' => 'private',
                ],
                [
                    'name' => 'currency',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The three-letter currency code used for item values, such as USD or EUR.',
                    'example' => 'USD',
                ],
            ],
            'returns' => 'The updated collection object.',
            'response' => ['data' => $collection('1', 'My Comics', 'Silver age Marvel issues.', '📚', 'private')],
        ],
        [
            'id' => 'collections-destroy',
            'title' => 'Delete a collection',
            'label' => 'Delete a collection',
            'method' => 'DELETE',
            'path' => '/collections/{collection}',
            'examplePath' => '/collections/1',
            'description' => 'Delete a collection.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
