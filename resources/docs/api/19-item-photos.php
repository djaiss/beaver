<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$photo = fn (string $id, bool $isMain, int $position): array => [
    'type' => 'item_photo',
    'id' => $id,
    'attributes' => [
        'item_id' => '1',
        'filename' => 'cover.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 204800,
        'is_main' => $isMain,
        'position' => $position,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/items/1/photos/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of photos to return per page, between 1 and 100.',
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

$itemId = [
    'name' => 'item',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the item the photo belongs to.',
];

$photoId = [
    'name' => 'photo',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the photo.',
];

return [
    'name' => 'Item photos',
    'sections' => [
        [
            'id' => 'item-photos-list',
            'title' => 'List item photos',
            'method' => 'GET',
            'path' => '/items/{item}/photos',
            'examplePath' => '/items/1/photos',
            'description' => 'Retrieve the photos of an item, in the order the user arranged them. Exactly one photo is the main visual.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of item photo objects.',
            'response' => ApiDocumentation::paginated([
                $photo('1', true, 1),
                $photo('2', false, 2),
            ], '/items/1/photos'),
        ],
        [
            'id' => 'item-photos-show',
            'title' => 'Get an item photo',
            'label' => 'Get an item photo',
            'method' => 'GET',
            'path' => '/items/{item}/photos/{photo}',
            'examplePath' => '/items/1/photos/1',
            'description' => 'Retrieve the metadata of a single item photo by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId, $photoId],
            'returns' => 'An item photo object, or 404 when the photo does not belong to your account.',
            'response' => ['data' => $photo('1', true, 1)],
        ],
        [
            'id' => 'item-photos-create',
            'title' => 'Upload an item photo',
            'label' => 'Upload an item photo',
            'method' => 'POST',
            'path' => '/items/{item}/photos',
            'examplePath' => '/items/1/photos',
            'description' => 'Upload a photo for an item, as multipart/form-data. The first photo added to an item becomes its main visual.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId],
            'bodyParams' => [
                [
                    'name' => 'file',
                    'type' => 'file',
                    'required' => true,
                    'description' => 'The image file to upload. Maximum 10 MB.',
                ],
            ],
            'returns' => 'The created item photo object.',
            'responseStatus' => 201,
            'response' => ['data' => $photo('1', true, 1)],
        ],
        [
            'id' => 'item-photos-main',
            'title' => 'Set the main photo',
            'label' => 'Set the main photo',
            'method' => 'PUT',
            'path' => '/items/{item}/photos/{photo}/main',
            'examplePath' => '/items/1/photos/2/main',
            'description' => 'Promote a photo to be the main visual of its item. The previous main photo is demoted.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId, $photoId],
            'returns' => 'The updated item photo object.',
            'response' => ['data' => $photo('2', true, 2)],
        ],
        [
            'id' => 'item-photos-destroy',
            'title' => 'Delete an item photo',
            'label' => 'Delete an item photo',
            'method' => 'DELETE',
            'path' => '/items/{item}/photos/{photo}',
            'examplePath' => '/items/1/photos/1',
            'description' => 'Delete a photo, removing its file from storage. When the main photo is deleted, the next photo by position becomes the main visual.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$itemId, $photoId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
