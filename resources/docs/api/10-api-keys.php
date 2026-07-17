<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$apiKey = fn (string $id, string $name, ?string $token): array => [
    'type' => 'api_key',
    'id' => $id,
    'attributes' => [
        'name' => $name,
        'token' => $token,
        'last_used_at' => 1752537600,
        'created_at' => 1752537600,
        'updated_at' => 1752537600,
    ],
    'links' => [
        'self' => $base.'/administration/api/'.$id,
    ],
];

$keyId = [
    'name' => 'id',
    'type' => 'integer',
    'required' => true,
    'description' => 'The ID of the API key.',
];

return [
    'name' => 'API keys',
    'sections' => [
        [
            'id' => 'api-keys-list',
            'title' => 'List API keys',
            'label' => 'List keys',
            'method' => 'GET',
            'path' => '/administration/api',
            'description' => 'Retrieve every API key of your user, including the ones created by logging in. The list is not paginated, and the token attribute is always null: the plain-text token is only shown once, when the key is created.',
            'returns' => 'A list of api_key objects.',
            'response' => [
                'data' => [
                    $apiKey('1', 'GitHub Actions', null),
                    $apiKey('2', 'Login from My integration', null),
                ],
            ],
        ],
        [
            'id' => 'api-keys-show',
            'title' => 'Get an API key',
            'label' => 'Get a key',
            'method' => 'GET',
            'path' => '/administration/api/{id}',
            'examplePath' => '/administration/api/1',
            'description' => 'Retrieve a single API key of your user by its ID.',
            'pathParams' => [$keyId],
            'returns' => 'An api_key object, or 404 when the key belongs to another user.',
            'response' => ['data' => $apiKey('1', 'GitHub Actions', null)],
        ],
        [
            'id' => 'api-keys-create',
            'title' => 'Create an API key',
            'label' => 'Create a key',
            'method' => 'POST',
            'path' => '/administration/api',
            'description' => 'Create a new API key for your user. The plain-text token is returned once in this response and cannot be retrieved later, so store it somewhere safe.',
            'bodyParams' => [
                [
                    'name' => 'label',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'A name identifying what the key is used for. Maximum 255 characters.',
                    'example' => 'GitHub Actions',
                ],
            ],
            'returns' => 'The created api_key object. The plain-text token appears in the token attribute and at the top level of the response.',
            'responseStatus' => 201,
            'response' => [
                'data' => $apiKey('1', 'GitHub Actions', '3|aB4cD5eF6gH7iJ8kL9mN0oP1qR2sT3uV4wX5yZ6b'),
                'token' => '3|aB4cD5eF6gH7iJ8kL9mN0oP1qR2sT3uV4wX5yZ6b',
            ],
        ],
        [
            'id' => 'api-keys-destroy',
            'title' => 'Delete an API key',
            'label' => 'Delete a key',
            'method' => 'DELETE',
            'path' => '/administration/api/{id}',
            'examplePath' => '/administration/api/1',
            'description' => 'Revoke an API key. Requests made with the revoked token stop working immediately.',
            'pathParams' => [$keyId],
            'returns' => 'An empty response.',
            'responseStatus' => 204,
        ],
    ],
];
