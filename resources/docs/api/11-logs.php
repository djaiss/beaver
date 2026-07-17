<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$log = fn (string $id, string $action, array $parameters, string $description): array => [
    'type' => 'log',
    'id' => $id,
    'attributes' => [
        'user_name' => 'Monica Geller',
        'action' => $action,
        'parameters' => $parameters,
        'description' => $description,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/administration/logs/'.$id,
    ],
];

return [
    'name' => 'Logs',
    'sections' => [
        [
            'id' => 'logs-list',
            'title' => 'List logs',
            'method' => 'GET',
            'path' => '/administration/logs',
            'description' => 'Retrieve the audit trail of actions performed by your user, most recent first. Logs are created automatically for every action; they cannot be created, changed or deleted through the API.',
            'queryParams' => [
                [
                    'name' => 'per_page',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The number of logs to return per page, between 1 and 100.',
                    'default' => '10',
                ],
                [
                    'name' => 'page',
                    'type' => 'integer',
                    'required' => false,
                    'description' => 'The page number to return.',
                    'default' => '1',
                ],
            ],
            'returns' => 'A paginated list of log objects.',
            'response' => ApiDocumentation::paginated([
                $log('2', 'collection_creation', ['name' => 'My Comics'], 'Created the collection My Comics'),
                $log('1', 'account_creation', [], 'Created the account'),
            ], '/administration/logs'),
        ],
        [
            'id' => 'logs-show',
            'title' => 'Get a log',
            'label' => 'Get a log',
            'method' => 'GET',
            'path' => '/administration/logs/{log}',
            'examplePath' => '/administration/logs/2',
            'description' => 'Retrieve a single log entry of your user by its ID.',
            'pathParams' => [
                [
                    'name' => 'log',
                    'type' => 'integer',
                    'required' => true,
                    'description' => 'The ID of the log entry.',
                ],
            ],
            'returns' => 'A log object, or 404 when the log belongs to another user.',
            'response' => ['data' => $log('2', 'collection_creation', ['name' => 'My Comics'], 'Created the collection My Comics')],
        ],
    ],
];
