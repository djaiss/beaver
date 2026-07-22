<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$set = fn (string $id, string $name, ?string $description): array => [
    'type' => 'set',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'description' => $description,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/sets/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of sets to return per page, between 1 and 100.',
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

$setId = [
    'name' => 'set',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the set.',
];

$nameParam = [
    'name' => 'name',
    'type' => 'string',
    'required' => true,
    'description' => 'The name of the set. Maximum 255 characters.',
    'example' => 'Amazing Spider-Man #1-10',
];

$descriptionParam = [
    'name' => 'description',
    'type' => 'string',
    'required' => false,
    'description' => 'A free text description of the set. Maximum 2000 characters.',
    'example' => 'The first ten issues of the run.',
];

return [
    'name' => 'Sets',
    'sections' => [
        [
            'id' => 'sets-list',
            'title' => 'List sets',
            'method' => 'GET',
            'path' => '/sets',
            'description' => 'Retrieve the sets of your account. A set groups items collected together as a series, e.g. Amazing Spider-Man #1-10, and is used to track completion.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of set objects.',
            'response' => ApiDocumentation::paginated([
                $set('1', 'Amazing Spider-Man #1-10', 'The first ten issues of the run.'),
                $set('2', 'Beatles studio albums', null),
            ], '/sets'),
        ],
        [
            'id' => 'sets-show',
            'title' => 'Get a set',
            'label' => 'Get a set',
            'method' => 'GET',
            'path' => '/sets/{set}',
            'examplePath' => '/sets/1',
            'description' => 'Retrieve a single set of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$setId],
            'returns' => 'A set object, or 404 when the set does not belong to your account.',
            'response' => ['data' => $set('1', 'Amazing Spider-Man #1-10', 'The first ten issues of the run.')],
        ],
        [
            'id' => 'sets-create',
            'title' => 'Create a set',
            'label' => 'Create a set',
            'method' => 'POST',
            'path' => '/sets',
            'description' => 'Create a set for your account.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [$nameParam, $descriptionParam],
            'returns' => 'The created set object.',
            'responseStatus' => 201,
            'response' => ['data' => $set('1', 'Amazing Spider-Man #1-10', 'The first ten issues of the run.')],
        ],
        [
            'id' => 'sets-update',
            'title' => 'Update a set',
            'label' => 'Update a set',
            'method' => 'PUT',
            'path' => '/sets/{set}',
            'examplePath' => '/sets/1',
            'description' => 'Update the name and description of a set.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$setId],
            'bodyParams' => [$nameParam, $descriptionParam],
            'returns' => 'The updated set object.',
            'response' => ['data' => $set('1', 'Amazing Spider-Man #1-10', 'The first ten issues of the run.')],
        ],
        [
            'id' => 'sets-destroy',
            'title' => 'Delete a set',
            'label' => 'Delete a set',
            'method' => 'DELETE',
            'path' => '/sets/{set}',
            'examplePath' => '/sets/1',
            'description' => 'Delete a set.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$setId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
