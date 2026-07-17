<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$tag = fn (string $id, string $name): array => [
    'type' => 'tag',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/tags/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of tags to return per page, between 1 and 100.',
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

$tagId = [
    'name' => 'tag',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the tag.',
];

return [
    'name' => 'Tags',
    'sections' => [
        [
            'id' => 'tags-list',
            'title' => 'List tags',
            'method' => 'GET',
            'path' => '/tags',
            'description' => 'Retrieve the tags of your account: free-form labels reusable across all your collections, e.g. Signed or First Issue.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of tag objects.',
            'response' => ApiDocumentation::paginated([
                $tag('1', 'Signed'),
                $tag('2', 'First Issue'),
            ], '/tags'),
        ],
        [
            'id' => 'tags-show',
            'title' => 'Get a tag',
            'label' => 'Get a tag',
            'method' => 'GET',
            'path' => '/tags/{tag}',
            'examplePath' => '/tags/2',
            'description' => 'Retrieve a single tag of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$tagId],
            'returns' => 'A tag object, or 404 when the tag does not belong to your account.',
            'response' => ['data' => $tag('2', 'First Issue')],
        ],
        [
            'id' => 'tags-create',
            'title' => 'Create a tag',
            'label' => 'Create a tag',
            'method' => 'POST',
            'path' => '/tags',
            'description' => 'Create a tag for your account.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the tag. Maximum 255 characters.',
                    'example' => 'First Issue',
                ],
            ],
            'returns' => 'The created tag object.',
            'responseStatus' => 201,
            'response' => ['data' => $tag('2', 'First Issue')],
        ],
        [
            'id' => 'tags-update',
            'title' => 'Update a tag',
            'label' => 'Update a tag',
            'method' => 'PUT',
            'path' => '/tags/{tag}',
            'examplePath' => '/tags/2',
            'description' => 'Update the name of a tag.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$tagId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the tag. Maximum 255 characters.',
                    'example' => 'First Issue',
                ],
            ],
            'returns' => 'The updated tag object.',
            'response' => ['data' => $tag('2', 'First Issue')],
        ],
        [
            'id' => 'tags-destroy',
            'title' => 'Delete a tag',
            'label' => 'Delete a tag',
            'method' => 'DELETE',
            'path' => '/tags/{tag}',
            'examplePath' => '/tags/1',
            'description' => 'Delete a tag.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$tagId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
