<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$field = fn (string $id, string $name, string $fieldType, ?array $options, int $position, ?string $groupId = null): array => [
    'type' => 'custom_field',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'field_type' => $fieldType,
        'options' => $options,
        'position' => $position,
        'group_id' => $groupId,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/collection-types/1/custom-fields/'.$id,
    ],
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of custom fields to return per page, between 1 and 100.',
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
    'description' => 'The ID of the collection type the field belongs to.',
];

$fieldId = [
    'name' => 'customField',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the custom field.',
];

return [
    'name' => 'Custom fields',
    'sections' => [
        [
            'id' => 'custom-fields-list',
            'title' => 'List custom fields',
            'label' => 'List fields',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}/custom-fields',
            'examplePath' => '/collection-types/1/custom-fields',
            'description' => 'Retrieve the custom fields of a collection type, in position order. A custom field is a field definition on a type, such as Issue # on Comics or Vintage on Wine. Its group_id tells you the group it sits in, and is null when the field is standalone.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$typeId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of custom_field objects.',
            'response' => ApiDocumentation::paginated([
                $field('1', 'Issue #', 'number', null, 1, '1'),
                $field('2', 'Grade', 'select', ['NM', 'VF', 'FN'], 1),
            ], '/collection-types/1/custom-fields'),
        ],
        [
            'id' => 'custom-fields-show',
            'title' => 'Get a custom field',
            'label' => 'Get a field',
            'method' => 'GET',
            'path' => '/collection-types/{collectionType}/custom-fields/{customField}',
            'examplePath' => '/collection-types/1/custom-fields/2',
            'description' => 'Retrieve a single custom field by its ID. The field must belong to the collection type in the URL.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$typeId, $fieldId],
            'returns' => 'A custom_field object, or 404 when the field or the type does not belong to your account.',
            'response' => ['data' => $field('2', 'Grade', 'select', ['NM', 'VF', 'FN'], 2)],
        ],
        [
            'id' => 'custom-fields-create',
            'title' => 'Create a custom field',
            'label' => 'Create a field',
            'method' => 'POST',
            'path' => '/collection-types/{collectionType}/custom-fields',
            'examplePath' => '/collection-types/1/custom-fields',
            'description' => 'Create a custom field on a collection type. The field is appended after the existing ones.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The name of the field. Maximum 255 characters.',
                    'example' => 'Grade',
                ],
                [
                    'name' => 'field_type',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The kind of value the field holds. One of text, number, date, boolean or select.',
                    'example' => 'select',
                ],
                [
                    'name' => 'options',
                    'type' => 'array of strings',
                    'required' => false,
                    'description' => 'The choices of a select field. Blank entries are removed, and the parameter is ignored for the other field types.',
                    'example' => ['NM', 'VF', 'FN'],
                ],
                [
                    'name' => 'group_id',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The ID of the custom field group to place the field in. The group must belong to the same collection type. When omitted, the field is standalone and sits directly on the type.',
                    'example' => 1,
                ],
            ],
            'returns' => 'The created custom_field object.',
            'responseStatus' => 201,
            'response' => ['data' => $field('2', 'Grade', 'select', ['NM', 'VF', 'FN'], 2)],
        ],
        [
            'id' => 'custom-fields-update',
            'title' => 'Update a custom field',
            'label' => 'Update a field',
            'method' => 'PUT',
            'path' => '/collection-types/{collectionType}/custom-fields/{customField}',
            'examplePath' => '/collection-types/1/custom-fields/2',
            'description' => 'Update a custom field. Use position to reorder the fields: they are shown in ascending position order within their group, or within the type when the field is standalone. A field cannot be moved between groups.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId, $fieldId],
            'bodyParams' => [
                [
                    'name' => 'name',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The name of the field. Maximum 255 characters.',
                    'example' => 'Grade',
                ],
                [
                    'name' => 'field_type',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The kind of value the field holds. One of text, number, date, boolean or select.',
                    'example' => 'select',
                ],
                [
                    'name' => 'options',
                    'type' => 'array of strings',
                    'required' => false,
                    'description' => 'The choices of a select field. Blank entries are removed, and the parameter is ignored for the other field types.',
                    'example' => ['NM', 'VF', 'FN'],
                ],
                [
                    'name' => 'position',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The position of the field within its group, or within the type when the field is standalone, starting at 1. When omitted, the current position is kept.',
                    'example' => 2,
                ],
            ],
            'returns' => 'The updated custom_field object.',
            'response' => ['data' => $field('2', 'Grade', 'select', ['NM', 'VF', 'FN'], 2)],
        ],
        [
            'id' => 'custom-fields-destroy',
            'title' => 'Delete a custom field',
            'label' => 'Delete a field',
            'method' => 'DELETE',
            'path' => '/collection-types/{collectionType}/custom-fields/{customField}',
            'examplePath' => '/collection-types/1/custom-fields/2',
            'description' => 'Delete a custom field from a collection type.',
            'permissions' => 'Owners and editors. Viewers get a 404 response.',
            'pathParams' => [$typeId, $fieldId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
