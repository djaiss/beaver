<?php

declare(strict_types=1);

$entry = fn (string $id, string $objectType, string $name, ?string $subtitle, int $daysLeft): array => [
    'type' => 'trashed_object',
    'id' => $id,
    'attributes' => [
        'object_type' => $objectType,
        'name' => $name,
        'subtitle' => $subtitle,
        'deleted_at' => 1752537600,
        'deleted_by_name' => 'Rachel Green',
        'days_left' => $daysLeft,
    ],
];

return [
    'name' => 'Trash',
    'sections' => [
        [
            'id' => 'trash-list',
            'title' => 'List the trash',
            'method' => 'GET',
            'path' => '/trash',
            'description' => 'Retrieve everything your account has soft deleted and can still restore: collections, items, copies, categories and sets. The list is sorted by urgency, so whatever expires first comes first.',
            'body' => [
                'Deleting a parent does not stamp its children, so this only surfaces the objects someone deleted on purpose. Restoring a collection brings its items back with it.',
                'The list merges five tables into one, so it is returned whole rather than paginated. The id is the ID of the object in its own table, which means an id is only unique together with object_type.',
            ],
            'permissions' => 'Any member of the account.',
            'returns' => 'A list of trashed_object entries.',
            'response' => [
                'data' => [
                    $entry('7', 'item', 'Amazing Spider-Man #1', 'My Comics', 3),
                    $entry('2', 'collection', 'My Vinyl', null, 27),
                ],
            ],
        ],
        [
            'id' => 'trash-restore',
            'title' => 'Restore an object',
            'label' => 'Restore an object',
            'method' => 'PUT',
            'path' => '/trash',
            'description' => 'Move one object out of the trash and back to where it was. Identify it with the object_type and id pair returned by the list endpoint.',
            'body' => [
                'The response has no body: fetch the object from its own endpoint once it is back. Restoring an object whose parent is still in the trash restores the object alone, so it stays out of sight until the parent is restored too.',
            ],
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'bodyParams' => [
                [
                    'name' => 'type',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The kind of object to restore. One of collection, item, copy, category or set.',
                    'example' => 'item',
                ],
                [
                    'name' => 'id',
                    'type' => 'integer',
                    'required' => true,
                    'description' => 'The ID of the object to restore, within its own kind.',
                    'example' => 7,
                ],
            ],
            'returns' => 'An empty response, or 404 when nothing of that kind and ID sits in your trash.',
            'responseStatus' => 204,
        ],
        [
            'id' => 'trash-empty',
            'title' => 'Empty the trash',
            'label' => 'Empty the trash',
            'method' => 'DELETE',
            'path' => '/trash',
            'description' => 'Permanently delete everything in the trash of your account, across all five kinds of object at once.',
            'body' => [
                'This cannot be undone, and there is no confirmation step. Objects left in the trash are purged automatically once their retention window runs out, so emptying it by hand is only a way to reclaim the space sooner.',
            ],
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
