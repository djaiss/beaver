<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$condition = fn (string $id, string $name): array => [
    'type' => 'condition',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/conditions/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of conditions to return per page, between 1 and 100.',
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

$conditionId = [
    'name' => 'condition',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the condition.',
];

return [
    'name' => 'Conditions',
    'sections' => [
        [
            'id' => 'conditions-list',
            'title' => 'List conditions',
            'method' => 'GET',
            'path' => '/conditions',
            'description' => 'Retrieve the conditions of your account, e.g. New, Used or Damaged, used to describe the state of an item.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of condition objects.',
            'response' => ApiDocumentation::paginated([
                $condition('1', 'New'),
                $condition('2', 'Used'),
            ], '/conditions'),
        ],
        [
            'id' => 'conditions-show',
            'title' => 'Get a condition',
            'label' => 'Get a condition',
            'method' => 'GET',
            'path' => '/conditions/{condition}',
            'examplePath' => '/conditions/2',
            'description' => 'Retrieve a single condition of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$conditionId],
            'returns' => 'A condition object, or 404 when the condition does not belong to your account.',
            'response' => ['data' => $condition('2', 'Used')],
        ],
        [
            'id' => 'conditions-create',
            'title' => 'Create a condition',
            'label' => 'Create a condition',
            'method' => 'POST',
            'path' => '/conditions',
            'description' => 'Create a condition for your account.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the condition. Maximum 255 characters.',
                    'example' => 'Used',
                ],
            ],
            'returns' => 'The created condition object.',
            'responseStatus' => 201,
            'response' => ['data' => $condition('2', 'Used')],
        ],
        [
            'id' => 'conditions-update',
            'title' => 'Update a condition',
            'label' => 'Update a condition',
            'method' => 'PUT',
            'path' => '/conditions/{condition}',
            'examplePath' => '/conditions/2',
            'description' => 'Update the name of a condition.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$conditionId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The name of the condition. Maximum 255 characters.',
                    'example' => 'Used',
                ],
            ],
            'returns' => 'The updated condition object.',
            'response' => ['data' => $condition('2', 'Used')],
        ],
        [
            'id' => 'conditions-destroy',
            'title' => 'Delete a condition',
            'label' => 'Delete a condition',
            'method' => 'DELETE',
            'path' => '/conditions/{condition}',
            'examplePath' => '/conditions/1',
            'description' => 'Delete a condition.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$conditionId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
