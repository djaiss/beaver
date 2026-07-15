<?php

declare(strict_types=1);

return [
    'name' => 'Authentication',
    'sections' => [
        [
            'id' => 'auth-register',
            'title' => 'Register',
            'method' => 'POST',
            'path' => '/register',
            'auth' => false,
            'description' => 'Create a new user with its own account, and receive an API token for it. The token is named after device_name when provided. This endpoint is limited to 6 requests per minute.',
            'bodyParams' => [
                [
                    'name' => 'first_name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The first name of the user. Maximum 100 characters.',
                    'example' => 'Monica',
                ],
                [
                    'name' => 'last_name',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The last name of the user. Maximum 100 characters.',
                    'example' => 'Geller',
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The email address of the user. Must be lowercase, unique, and not from a disposable email provider.',
                    'example' => 'monica@example.com',
                ],
                [
                    'name' => 'password',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Minimum 8 characters. The password is checked against known data leaks, and a compromised password is rejected.',
                    'example' => 'a-very-strong-password',
                ],
                [
                    'name' => 'password_confirmation',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Must match password.',
                    'example' => 'a-very-strong-password',
                ],
                [
                    'name' => 'device_name',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'A label for the API token that is created, such as the name of your app or device.',
                    'example' => 'My integration',
                ],
            ],
            'returns' => 'A confirmation message and the API token to use on subsequent requests.',
            'responseStatus' => 201,
            'response' => [
                'message' => 'Account created',
                'status' => 201,
                'data' => [
                    'token' => '1|f9aB2cD3eF4gH5iJ6kL7mN8oP9qR0sT1uV2wX3yZ',
                ],
            ],
        ],
        [
            'id' => 'auth-login',
            'title' => 'Log in',
            'method' => 'POST',
            'path' => '/login',
            'auth' => false,
            'description' => 'Exchange credentials for an API token. Invalid credentials return 401. When two-factor authentication is enabled on the user, a valid TOTP or recovery code must be passed in code, otherwise the endpoint returns 401. This endpoint is limited to 6 requests per minute.',
            'bodyParams' => [
                [
                    'name' => 'email',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The email address of the user.',
                    'example' => 'monica@example.com',
                ],
                [
                    'name' => 'password',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The password of the user.',
                    'example' => 'a-very-strong-password',
                ],
                [
                    'name' => 'code',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'The TOTP or recovery code. Required when two-factor authentication is enabled on the user.',
                ],
                [
                    'name' => 'device_name',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'A label for the API token that is created, such as the name of your app or device.',
                    'example' => 'My integration',
                ],
            ],
            'returns' => 'A confirmation message and the API token to use on subsequent requests.',
            'response' => [
                'message' => 'Authenticated',
                'status' => 200,
                'data' => [
                    'token' => '2|aB3cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5a',
                ],
            ],
        ],
        [
            'id' => 'auth-logout',
            'title' => 'Log out',
            'method' => 'DELETE',
            'path' => '/logout',
            'description' => 'Revoke the token used to make the request. Other tokens of the user stay valid.',
            'returns' => 'A confirmation message.',
            'response' => [
                'message' => 'Logged out successfully',
                'status' => 200,
            ],
        ],
    ],
];
