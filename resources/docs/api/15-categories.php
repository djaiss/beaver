<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$category = fn (string $id, string $name, ?string $parentId, ?string $description = null): array => [
    'type' => 'category',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'description' => $description,
        'collection_id' => '1',
        'parent_id' => $parentId,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collections/1/categories/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of categories to return per page, between 1 and 100.',
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
    'description' => 'The ID of the collection the category belongs to.',
];

$categoryId = [
    'name' => 'category',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the category.',
];

$nameParam = [
    'name' => 'name',
    'type' => 'string',
    'required' => true,
    'description' => 'The name of the category. Maximum 255 characters.',
    'example' => 'Marvel',
];

$parentParam = [
    'name' => 'parent_id',
    'type' => 'integer',
    'required' => false,
    'description' => 'The ID of the parent category, for nesting. Must belong to the same collection.',
    'example' => '1',
];

$descriptionParam = [
    'name' => 'description',
    'type' => 'string',
    'required' => false,
    'description' => 'What the category holds, shown on the category page. Maximum 255 characters.',
    'example' => 'Key issues and full runs from the 1990s Marvel era.',
];

return [
    'name' => 'Categories',
    'sections' => [
        [
            'id' => 'categories-list',
            'title' => 'List categories',
            'method' => 'GET',
            'path' => '/collections/{collection}/categories',
            'examplePath' => '/collections/1/categories',
            'description' => 'Retrieve the categories of a collection. A category groups items within a collection, e.g. Marvel within a comics collection, and can be nested.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of category objects.',
            'response' => ApiDocumentation::paginated([
                $category('1', 'Marvel', null, 'Key issues and full runs from the 1990s Marvel era.'),
                $category('2', 'Spider-Man', '1'),
            ], '/collections/1/categories'),
        ],
        [
            'id' => 'categories-show',
            'title' => 'Get a category',
            'label' => 'Get a category',
            'method' => 'GET',
            'path' => '/collections/{collection}/categories/{category}',
            'examplePath' => '/collections/1/categories/1',
            'description' => 'Retrieve a single category of a collection by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$collectionId, $categoryId],
            'returns' => 'A category object, or 404 when the category does not belong to your account.',
            'response' => ['data' => $category('1', 'Marvel', null, 'Key issues and full runs from the 1990s Marvel era.')],
        ],
        [
            'id' => 'categories-create',
            'title' => 'Create a category',
            'label' => 'Create a category',
            'method' => 'POST',
            'path' => '/collections/{collection}/categories',
            'examplePath' => '/collections/1/categories',
            'description' => 'Create a category within a collection.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId],
            'bodyParams' => [$nameParam, $parentParam, $descriptionParam],
            'returns' => 'The created category object.',
            'responseStatus' => 201,
            'response' => ['data' => $category('1', 'Marvel', null, 'Key issues and full runs from the 1990s Marvel era.')],
        ],
        [
            'id' => 'categories-update',
            'title' => 'Update a category',
            'label' => 'Update a category',
            'method' => 'PUT',
            'path' => '/collections/{collection}/categories/{category}',
            'examplePath' => '/collections/1/categories/2',
            'description' => 'Update the name, description and parent of a category. A category cannot be its own parent, nor be nested under one of its own descendants.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId, $categoryId],
            'bodyParams' => [$nameParam, $parentParam, $descriptionParam],
            'returns' => 'The updated category object.',
            'response' => ['data' => $category('2', 'Spider-Man', '1')],
        ],
        [
            'id' => 'categories-destroy',
            'title' => 'Delete a category',
            'label' => 'Delete a category',
            'method' => 'DELETE',
            'path' => '/collections/{collection}/categories/{category}',
            'examplePath' => '/collections/1/categories/2',
            'description' => 'Delete a category. Its nested child categories are deleted as well.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$collectionId, $categoryId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
