<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$group = fn (string $id, string $name, int $position): array => [
    'type' => 'custom_field_group',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'position' => $position,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collection-types/1/custom-field-groups/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of groups to return per page, between 1 and 100.',
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
    'description' => 'The ID of the collection type the group belongs to.',
];

$groupId = [
    'name' => 'group',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the custom field group.',
];

$name = [
    'name' => 'name',
    'type' => 'string',
    'required' => false,
    'description' => 'The name of the group. Maximum 255 characters.',
    'example' => 'Publishing info',
];

return [
    'name' => 'Custom field groups',
    'sections' => [
        [
            'id' => 'custom-field-groups-list',
            'title' => 'List custom field groups',
            'label' => 'List groups',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}/custom-field-groups',
            'examplePath' => '/collection-types/1/custom-field-groups',
            'description' => 'Retrieve the custom field groups of a collection type, in position order. A group is a named section of custom fields within a type, such as Publishing info on Comics. Groups are optional: a type with no groups shows its fields as a flat list. Where a type has both, the fields with no group come first, followed by each group.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$typeId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of custom_field_group objects.',
            'response' => ApiDocumentation::paginated([
                $group('1', 'Publishing info', 1),
                $group('2', 'Condition & grading', 2),
            ], '/collection-types/1/custom-field-groups'),
        ],
        [
            'id' => 'custom-field-groups-show',
            'title' => 'Get a custom field group',
            'label' => 'Get a group',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}/custom-field-groups/{group}',
            'examplePath' => '/collection-types/1/custom-field-groups/1',
            'description' => 'Retrieve a single custom field group by its ID. The group must belong to the collection type in the URL.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$typeId, $groupId],
            'returns' => 'A custom_field_group object, or 404 when the group or the type does not belong to your account.',
            'response' => ['data' => $group('1', 'Publishing info', 1)],
        ],
        [
            'id' => 'custom-field-groups-create',
            'title' => 'Create a custom field group',
            'label' => 'Create a group',
            'method' => 'POST',
            'path' => '/collection-types/{collectionType}/custom-field-groups',
            'examplePath' => '/collection-types/1/custom-field-groups',
            'description' => 'Create a custom field group on a collection type. The group is appended after the existing ones. To place a field inside it, pass its ID as group_id when creating the custom field.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'bodyParams' => [$name],
            'returns' => 'The created custom_field_group object.',
            'responseStatus' => 201,
            'response' => ['data' => $group('1', 'Publishing info', 1)],
        ],
        [
            'id' => 'custom-field-groups-update',
            'title' => 'Update a custom field group',
            'label' => 'Update a group',
            'method' => 'PUT',
            'path' => '/collection-types/{collectionType}/custom-field-groups/{group}',
            'examplePath' => '/collection-types/1/custom-field-groups/1',
            'description' => 'Rename a custom field group. The fields it holds are left alone.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId, $groupId],
            'bodyParams' => [$name],
            'returns' => 'The updated custom_field_group object.',
            'response' => ['data' => $group('1', 'Publishing info', 1)],
        ],
        [
            'id' => 'custom-field-groups-destroy',
            'title' => 'Delete a custom field group',
            'label' => 'Delete a group',
            'method' => 'DELETE',
            'path' => '/collection-types/{collectionType}/custom-field-groups/{group}',
            'examplePath' => '/collection-types/1/custom-field-groups/1',
            'description' => 'Delete a custom field group. The fields it held are not deleted with it: their group_id becomes null and they become standalone fields on the type, so no value recorded against them is lost.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId, $groupId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
        [
            'id' => 'custom-field-groups-order',
            'title' => 'Move a custom field group',
            'label' => 'Move a group',
            'method' => 'PUT',
            'path' => '/collection-types/{collectionType}/custom-field-groups/{group}/order',
            'examplePath' => '/collection-types/1/custom-field-groups/1/order',
            'description' => 'Move a group one step up or down among the groups of its type. This is how the order of the sections on an item form is changed.',
            'body' => [
                'Moving a group that is already first up, or already last down, leaves the order untouched and still returns a 200 response.',
            ],
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId, $groupId],
            'bodyParams' => [
                [
                    'name' => 'direction',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The direction to move the group in. One of up or down.',
                    'example' => 'down',
                ],
            ],
            'returns' => 'The moved custom_field_group object, with its new position.',
            'response' => ['data' => $group('1', 'Publishing info', 1)],
        ],
    ],
];
