<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$series = fn (string $id, string $name, ?string $description): array => [
    'type' => 'series',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'description' => $description,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/series/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of series to return per page, between 1 and 100.',
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

$seriesId = [
    'name' => 'series',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the series.',
];

$nameParam = [
    'name' => 'name',
    'type' => 'string',
    'required' => true,
    'description' => 'The name of the series. Maximum 255 characters.',
    'example' => 'Harry Potter',
];

$descriptionParam = [
    'name' => 'description',
    'type' => 'string',
    'required' => false,
    'description' => 'A free text description of the series. Maximum 2000 characters.',
    'example' => 'The wizarding world across books, films and collectibles.',
];

return [
    'name' => 'Series',
    'sections' => [
        [
            'id' => 'series-list',
            'title' => 'List series',
            'method' => 'GET',
            'path' => '/series',
            'description' => 'Retrieve the series of your account. A series links related items into a broader franchise or body of work, e.g. Harry Potter or Star Wars, and may span several collections. Unlike a set, a series has no target and tracks no completion. Link an item to a series with the series_id attribute of the item endpoints.',
            'permissions' => 'Any member of the account.',
            'queryParams' => $pagination,
            'returns' => 'A paginated list of series objects.',
            'response' => ApiDocumentation::paginated([
                $series('1', 'Harry Potter', 'The wizarding world across books, films and collectibles.'),
                $series('2', 'Star Wars', null),
            ], '/series'),
        ],
        [
            'id' => 'series-show',
            'title' => 'Get a series',
            'label' => 'Get a series',
            'method' => 'GET',
            'path' => '/series/{series}',
            'examplePath' => '/series/1',
            'description' => 'Retrieve a single series of your account by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$seriesId],
            'returns' => 'A series object, or 404 when the series does not belong to your account.',
            'response' => ['data' => $series('1', 'Harry Potter', 'The wizarding world across books, films and collectibles.')],
        ],
        [
            'id' => 'series-create',
            'title' => 'Create a series',
            'label' => 'Create a series',
            'method' => 'POST',
            'path' => '/series',
            'description' => 'Create a series for your account. A series is account-wide, so it takes no collection.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [$nameParam, $descriptionParam],
            'returns' => 'The created series object.',
            'responseStatus' => 201,
            'response' => ['data' => $series('1', 'Harry Potter', 'The wizarding world across books, films and collectibles.')],
        ],
        [
            'id' => 'series-update',
            'title' => 'Update a series',
            'label' => 'Update a series',
            'method' => 'PUT',
            'path' => '/series/{series}',
            'examplePath' => '/series/1',
            'description' => 'Update the name and description of a series.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$seriesId],
            'bodyParams' => [$nameParam, $descriptionParam],
            'returns' => 'The updated series object.',
            'response' => ['data' => $series('1', 'Harry Potter', 'The wizarding world across books, films and collectibles.')],
        ],
        [
            'id' => 'series-destroy',
            'title' => 'Delete a series',
            'label' => 'Delete a series',
            'method' => 'DELETE',
            'path' => '/series/{series}',
            'examplePath' => '/series/1',
            'description' => 'Delete a series. The items linked to it are unlinked, not deleted.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$seriesId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
