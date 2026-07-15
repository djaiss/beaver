<?php

declare(strict_types=1);

use App\Services\ApiDocumentation;

$base = ApiDocumentation::baseUrl();

$user = [
    'type' => 'user',
    'id' => '1',
    'attributes' => [
        'first_name' => 'Monica',
        'last_name' => 'Geller',
        'nickname' => 'Mon',
        'email' => 'monica@example.com',
        'locale' => 'en',
        'time_format_24h' => true,
    ],
    'links' => [
        'self' => $base.'/me',
    ],
];

return [
    'name' => 'Your profile',
    'sections' => [
        [
            'id' => 'me-show',
            'title' => 'Get your profile',
            'label' => 'Get your profile',
            'method' => 'GET',
            'path' => '/me',
            'description' => 'Retrieve the user the token belongs to.',
            'returns' => 'A user object.',
            'response' => ['data' => $user],
        ],
        [
            'id' => 'me-update',
            'title' => 'Update your profile',
            'label' => 'Update your profile',
            'method' => 'PUT',
            'path' => '/me',
            'description' => 'Update the profile of the user the token belongs to.',
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
                    'name' => 'nickname',
                    'type' => 'string',
                    'required' => false,
                    'description' => 'An optional nickname, shown instead of the full name when set.',
                    'example' => 'Mon',
                ],
                [
                    'name' => 'email',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The email address of the user. Must be lowercase, unique, and not from a disposable email provider.',
                    'example' => 'monica@example.com',
                ],
                [
                    'name' => 'locale',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'The locale of the user. One of en, fr_FR, es_ES or de_DE.',
                    'example' => 'en',
                ],
                [
                    'name' => 'time_format_24h',
                    'type' => 'string',
                    'required' => true,
                    'description' => 'Whether the user prefers the 24-hour time format. The string true or false.',
                    'example' => 'true',
                ],
            ],
            'returns' => 'The updated user object.',
            'response' => ['data' => $user],
        ],
    ],
];
