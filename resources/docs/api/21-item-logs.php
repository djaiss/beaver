<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$itemLog = fn (string $id, string $userName, string $action, ?array $parameters, string $description): array => [
    'type' => 'item_log',
    'id' => $id,
    'attributes' => [
        'user_name' => $userName,
        'action' => $action,
        'parameters' => $parameters,
        'description' => $description,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/items/1/logs/'.$id,
    ],
];

$itemId = [
    'name' => 'item',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the item the entry belongs to.',
];

$logId = [
    'name' => 'log',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the activity entry.',
];

$pagination = [
    [
        'name' => 'per_page',
        'type' => 'integer',
        'required' => false,
        'description' => 'The number of entries to return per page, between 1 and 100.',
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

$attributes = [
    'Each entry carries the `action` that was performed, the `user_name` of whoever performed it, and a `description` already translated into the language of your account. A user who has since been deleted leaves the name recorded at the time of the action.',
    'The `parameters` object holds whatever the action recorded, and its shape depends on the action. A `label` names something the action applied to, such as a tag. A `file` names an uploaded file. A `changes` array lists the values that moved, each with a `label` and the `from` and `to` values (either may be null when the value was not set). Treat `parameters` as free form: new keys may be added over time.',
];

return [
    'name' => 'Item activity',
    'sections' => [
        [
            'id' => 'item-logs-list',
            'title' => 'List the activity of an item',
            'label' => 'List item activity',
            'method' => 'GET',
            'path' => '/items/{item}/logs',
            'examplePath' => '/items/1/logs',
            'description' => 'Retrieve everything that has been done to an item, most recent first. Entries are recorded automatically as actions are performed, so they cannot be created, changed or deleted through the API.',
            'body' => $attributes,
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId],
            'queryParams' => $pagination,
            'returns' => 'A paginated list of item activity objects, or 404 when the item does not belong to your account.',
            'response' => ApiDocumentation::paginated([
                $itemLog('3', 'Monica Geller', 'copy_updated', ['changes' => [['label' => 'Estimated value', 'from' => '$390', 'to' => '$420']]], 'updated a copy'),
                $itemLog('2', 'Monica Geller', 'tag_attached', ['label' => 'Signed'], 'added the tag'),
                $itemLog('1', 'Rachel Green', 'item_created', null, 'created this item'),
            ], '/items/1/logs'),
        ],
        [
            'id' => 'item-logs-show',
            'title' => 'Get an activity entry',
            'label' => 'Get an activity entry',
            'method' => 'GET',
            'path' => '/items/{item}/logs/{log}',
            'examplePath' => '/items/1/logs/2',
            'description' => 'Retrieve a single entry of the activity of an item by its ID.',
            'permissions' => 'Any member of the account.',
            'pathParams' => [$itemId, $logId],
            'returns' => 'An item activity object, or 404 when the entry belongs to another item or to another account.',
            'response' => ['data' => $itemLog('2', 'Monica Geller', 'tag_attached', ['label' => 'Signed'], 'added the tag')],
        ],
    ],
];
